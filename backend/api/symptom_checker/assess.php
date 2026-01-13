<?php
/**
 * PSM System - Symptom Checker Assessment API
 * Uses Decision Tree Rules to assess symptoms and provide recommendations
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/functions.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mother') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data provided']);
    exit;
}

try {
    // Extract symptom data
    $symptoms = $data['symptoms'] ?? [];
    $temperature = floatval($data['temperature'] ?? 0);
    $pain_level = intval($data['pain_level'] ?? 0);
    $bleeding_status = $data['bleeding_status'] ?? 'normal';
    $wound_condition = $data['wound_condition'] ?? 'normal';
    $mood_status = $data['mood_status'] ?? 'normal';
    $user_id = $_SESSION['user_id'];

    // Decision Tree Rule Execution for Symptom Assessment
    $assessment_result = executeSymptomDecisionTree($symptoms, $temperature, $pain_level, $bleeding_status, $wound_condition, $mood_status);

    // Save the assessment to the database
    $stmt = $conn->prepare("INSERT INTO symptom_logs (user_id, week_postpartum, temperature, pain_level, mood_status, wound_condition, bleeding_status, result_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $week = intval($data['week_postpartum'] ?? 0);
    $result_status = $assessment_result['risk_level'];
    $stmt->bind_param("iidsssss", $user_id, $week, $temperature, $pain_level, $mood_status, $wound_condition, $bleeding_status, $result_status);
    $stmt->execute();
    $stmt->close();

    echo json_encode([
        'success' => true,
        'assessment' => $assessment_result
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'System error: ' . $e->getMessage()]);
}

/**
 * Execute the Symptom Decision Tree Rules
 */
function executeSymptomDecisionTree($symptoms, $temperature, $pain_level, $bleeding_status, $wound_condition, $mood_status) {
    // Initialize risk level and recommendation
    $risk_level = 'low';
    $recommendation = '';
    $action = '';
    $priority = 'routine';

    // Decision Tree Rule 1: Check for emergency symptoms
    if (hasEmergencySymptoms($symptoms)) {
        $risk_level = 'high';
        $recommendation = 'Seek immediate medical attention. This may be a medical emergency.';
        $action = 'emergency_consultation';
        $priority = 'immediate';
    }
    // Decision Tree Rule 2: Check for high-risk symptoms
    elseif (hasHighRiskSymptoms($symptoms, $temperature, $pain_level, $bleeding_status, $wound_condition)) {
        $risk_level = 'high';
        $recommendation = 'Contact your healthcare provider immediately. Urgent consultation recommended.';
        $action = 'urgent_consultation';
        $priority = 'urgent';
    }
    // Decision Tree Rule 3: Check for medium-risk symptoms
    elseif (hasMediumRiskSymptoms($symptoms, $temperature, $pain_level, $wound_condition, $mood_status)) {
        $risk_level = 'medium';
        $recommendation = 'Schedule a consultation with your healthcare provider soon.';
        $action = 'schedule_consultation';
        $priority = 'soon';
    }
    // Decision Tree Rule 4: Check for low-risk symptoms
    elseif (hasLowRiskSymptoms($symptoms, $mood_status)) {
        $risk_level = 'low';
        $recommendation = 'Continue monitoring symptoms. Maintain self-care practices.';
        $action = 'monitor_symptoms';
        $priority = 'monitor';
    }
    // Decision Tree Rule 5: Default to low risk if no concerning symptoms
    else {
        $risk_level = 'low';
        $recommendation = 'Symptoms appear to be within normal range. Continue regular postpartum care.';
        $action = 'normal_care';
        $priority = 'routine';
    }

    return [
        'risk_level' => $risk_level,
        'recommendation' => $recommendation,
        'action' => $action,
        'priority' => $priority,
        'symptoms' => $symptoms,
        'temperature' => $temperature,
        'pain_level' => $pain_level,
        'bleeding_status' => $bleeding_status,
        'wound_condition' => $wound_condition,
        'mood_status' => $mood_status
    ];
}

/**
 * Decision Tree Rule: Check for emergency symptoms
 */
function hasEmergencySymptoms($symptoms) {
    $emergency_symptoms = [
        'severe_chest_pain',
        'difficulty_breathing',
        'severe_headache',
        'vision_problems',
        'heavy_bleeding',
        'high_fever_over_39',
        'seizures',
        'loss_of_consciousness'
    ];

    foreach ($emergency_symptoms as $emergency_symptom) {
        if (in_array($emergency_symptom, $symptoms)) {
            return true;
        }
    }
    return false;
}

/**
 * Decision Tree Rule: Check for high-risk symptoms
 */
function hasHighRiskSymptoms($symptoms, $temperature, $pain_level, $bleeding_status, $wound_condition) {
    // High temperature (>38°C)
    if ($temperature > 38.0) {
        return true;
    }

    // Severe pain (8-10 on scale)
    if ($pain_level >= 8) {
        return true;
    }

    // Heavy bleeding
    if ($bleeding_status === 'heavy' || $bleeding_status === 'excessive') {
        return true;
    }

    // Wound infection signs
    if ($wound_condition === 'infected' || $wound_condition === 'severe') {
        return true;
    }

    // Specific high-risk symptoms
    $high_risk_symptoms = [
        'persistent_fever',
        'severe_abdominal_pain',
        'foul_smelling_discharge',
        'increased_heart_rate',
        'dizziness',
        'fainting'
    ];

    foreach ($high_risk_symptoms as $high_risk_symptom) {
        if (in_array($high_risk_symptom, $symptoms)) {
            return true;
        }
    }

    return false;
}

/**
 * Decision Tree Rule: Check for medium-risk symptoms
 */
function hasMediumRiskSymptoms($symptoms, $temperature, $pain_level, $wound_condition, $mood_status) {
    // Moderate temperature (37.5-38°C)
    if ($temperature >= 37.5 && $temperature <= 38.0) {
        return true;
    }

    // Moderate pain (5-7 on scale)
    if ($pain_level >= 5 && $pain_level <= 7) {
        return true;
    }

    // Moderate wound issues
    if ($wound_condition === 'swelling' || $wound_condition === 'mild_infection') {
        return true;
    }

    // Mood concerns
    if ($mood_status === 'depressed' || $mood_status === 'anxious' || $mood_status === 'overwhelmed') {
        return true;
    }

    // Medium-risk symptoms
    $medium_risk_symptoms = [
        'mild_fever',
        'moderate_abdominal_pain',
        'mild_headache',
        'mild_nausea',
        'mild_dizziness',
        'mood_swings',
        'sleep_difficulties',
        'appetite_changes'
    ];

    foreach ($medium_risk_symptoms as $medium_risk_symptom) {
        if (in_array($medium_risk_symptom, $symptoms)) {
            return true;
        }
    }

    return false;
}

/**
 * Decision Tree Rule: Check for low-risk symptoms
 */
function hasLowRiskSymptoms($symptoms, $mood_status) {
    // Mood fluctuations that are normal
    if ($mood_status === 'normal' || $mood_status === 'happy' || $mood_status === 'tired') {
        return true;
    }

    // Low-risk symptoms
    $low_risk_symptoms = [
        'mild_fatigue',
        'normal_discharge',
        'mild_breast_tenderness',
        'mild_abdominal_cramping',
        'normal_healing',
        'mild_bloating'
    ];

    foreach ($low_risk_symptoms as $low_risk_symptom) {
        if (in_array($low_risk_symptom, $symptoms)) {
            return true;
        }
    }

    return false;
}
?>