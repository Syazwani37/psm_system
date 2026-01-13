<?php
/**
 * PSM System - Symptom Checker Assessment API
 * Uses a Decision Tree with 10 Specific Rules
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
    $week_postpartum = intval($data['week_postpartum'] ?? 0);
    $user_id = $_SESSION['user_id'];

    // execute the decision tree
    $assessment_result = executeSymptomDecisionTree($symptoms, $temperature, $pain_level, $bleeding_status, $wound_condition, $mood_status, $week_postpartum);

    // Save logs
    $stmt = $conn->prepare("INSERT INTO symptom_logs (user_id, week_postpartum, temperature, pain_level, mood_status, wound_condition, bleeding_status, result_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $result_status = $assessment_result['risk_level'];
    
    // Convert boolean/custom statuses to db enum friendly values if needed, but here we store raw inputs mostly
    // We'll store the RISK LEVEL as result_status
    $stmt->bind_param("iidsssss", $user_id, $week_postpartum, $temperature, $pain_level, $mood_status, $wound_condition, $bleeding_status, $result_status);
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
 * 10 Rule-Based Decision Tree Logic
 */
function executeSymptomDecisionTree($symptoms, $temperature, $pain_level, $bleeding, $wound, $mood, $week) {
    
    // Defaults
    $risk_level = 'low';
    $recommendation = 'Symptoms appear normal. Continue monitoring.';
    $action = 'monitor';
    $matched_rule = 'General Check';

    /**
     * CATEGORY: CRITICAL EMERGENCY (High Risk)
     */

    // RULE 1: Pulmonary Embolism Risk (Difficulty Breathing + Chest Pain)
    if (in_array('difficulty_breathing', $symptoms) && in_array('severe_chest_pain', $symptoms)) {
        return buildResponse('high', 'CRITICAL: Symptoms suggest Respiratory Distress. Call Ambulance immediately.', 'emergency_consultation', 'Rule 1: PE Risk');
    }

    // RULE 2: Pre-eclampsia Warning (Severe Headache + Vision Problems)
    if (in_array('severe_headache', $symptoms) && in_array('vision_problems', $symptoms)) {
        return buildResponse('high', 'URGENT: Potential Pre-eclampsia signs detected. Go to Emergency.', 'emergency_consultation', 'Rule 2: Pre-eclampsia');
    }

    // RULE 3: Postpartum Hemorrhage Risk (Heavy Bleeding + Dizziness)
    if (($bleeding === 'heavy' || $bleeding === 'excessive') && (in_array('dizziness', $symptoms) || in_array('fainting', $symptoms))) {
        return buildResponse('high', 'URGENT: Excessive blood loss symptoms. Seek medical help immediately.', 'emergency_consultation', 'Rule 3: Hemorrhage');
    }

    /**
     * CATEGORY: ACUTE INFECTION (High/Medium Risk)
     */

    // RULE 4: Suspected Endometritis (Fever + Foul Smell)
    if ($temperature >= 38.0 && in_array('foul_smelling_discharge', $symptoms)) {
        return buildResponse('high', 'High fever with foul discharge suggests Uterine Infection. Consult doctor.', 'urgent_consultation', 'Rule 4: Endometritis');
    }

    // RULE 5: Suspected Mastitis (Fever + Breast Pain)
    if ($temperature >= 38.0 && in_array('breast_pain', $symptoms)) {
        return buildResponse('high', 'Fever with breast pain suggests Mastitis. Continue breastfeeding and see a doctor.', 'urgent_consultation', 'Rule 5: Mastitis');
    }

    // RULE 6: Wound Infection (Fever + Wound Issues)
    if ($temperature >= 37.5 && ($wound === 'no' || in_array('wound_issues', $symptoms))) { // wound='no' means 'Red/Swollen' in frontend value map
        return buildResponse('medium', 'Signs of potential wound infection (C-sec/Episiotomy).', 'schedule_consultation', 'Rule 6: Wound Infection');
    }

    /**
     * CATEGORY: MENTAL HEALTH (Medium Risk)
     */

    // RULE 7: Postpartum Depression Risk (Sad Mood + Sleep Issues)
    if (($mood === 'sad' || $mood === 'very sad') && in_array('sleep_difficulties', $symptoms)) {
        return buildResponse('medium', 'You may be experiencing Postpartum Depression symptoms. Please seek support.', 'schedule_consultation', 'Rule 7: PPD Risk');
    }

    // RULE 8: High Anxiety State (Anxious + Fast Heartbeat)
    if (($mood === 'sad' || $mood === 'very sad') && in_array('increased_heart_rate', $symptoms)) { 
        // Note: 'sad' map captures 'Anxious' in frontend logic if not distinct. 
        // Frontend logic maps: sad -> "Sad / Anxious". very sad -> "Overwhelmed".
        return buildResponse('medium', 'Physical symptoms of anxiety detected. Practice deep breathing.', 'schedule_consultation', 'Rule 8: Anxiety');
    }

    /**
     * CATEGORY: GENERAL RECOVERY (Medium/Low Risk)
     */

    // RULE 9: Delayed Recovery (Bleeding > 6 Weeks)
    if ($week > 6 && $bleeding !== 'none') {
        return buildResponse('medium', 'Bleeding should likely have stopped by week 6. Consult a doctor.', 'schedule_consultation', 'Rule 9: Delayed Recovery');
    }

    // RULE 10: Physical Exhaustion (Pain + Fatigue)
    if (($pain_level >= 1) && in_array('mild_fatigue', $symptoms)) {
        return buildResponse('low', 'You are experiencing normal postpartum fatigue. Ensure you get rest.', 'monitor', 'Rule 10: Exhaustion');
    }

    // FALLBACK: GENERAL CHECK
    if ($temperature > 37.5 || $pain_level > 6 || $bleeding === 'heavy') {
        return buildResponse('medium', 'Abnormal readings detected. Monitor closely.', 'monitor', 'General Check');
    }

    return buildResponse('low', 'Your recovery appears to be on track.', 'monitor', 'Normal');
}

function buildResponse($risk, $rec, $act, $rule) {
    return [
        'risk_level' => $risk,
        'recommendation' => $rec,
        'action' => $act,
        'matched_rule' => $rule
    ];
}
?>