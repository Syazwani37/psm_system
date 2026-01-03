# Chapter 4: System Implementation

## 4.1 Introduction
This chapter details the technical implementation of the **Postpartum System Management (PSM) System**, a web-based application designed to assist mothers in their postpartum recovery journey. The system facilitates health tracking, resource access, and communication between mothers and healthcare professionals. The implementation focuses on a user-friendly interface, role-based access control, and responsive feature sets.

## 4.2 System Architecture & Technology Stack
The PSM System is built using a modern web development stack:
-   **Frontend**: HTML5 for semantic structure, CSS3 (Vanilla + FontAwesome) for styling, and JavaScript (Vanilla) for client-side logic.
-   **Backend Simulation**: `localStorage` is used to simulate backend persistence, allowing the prototype to function without a live server environment during the development phase.
-   **Database Design**: A MySQL schema (`psm_system.sql`) has been designed for future integration.

---

## 4.3 Module Implementation

### 4.3.1 User Authentication Module
The authentication module secures the system by ensuring only registered users can access specific dashboards. It handles Registration, Login, and Session Management.

**Key File**: `auth/login.html`
**Logic**: The system validates user credentials against stored records. Code snippet showing the login verification logic:

```javascript
function handleLogin(event) {
  event.preventDefault();
  const email = document.getElementById('email').value.trim();
  const password = document.getElementById('password').value;

  // Retrieve users from local storage simulation
  const users = JSON.parse(localStorage.getItem("users")) || [];
  const matchedUser = users.find(u => u.email === email && u.password === password);

  if (matchedUser) {
    // Create session
    localStorage.setItem('user', JSON.stringify(matchedUser));
    // Redirect based on role
    redirectToDashboard(matchedUser.role);
  } else {
    alert("❌ Invalid email or password.");
  }
}
```

### 4.3.2 Dashboard Framework
The system features distinct dashboards for each user role, providing a tailored experience.

#### A. Mother Dashboard
**Key File**: `dashboards/mother_dashboard.html`
The mother's dashboard serves as a personalized landing page. It features a responsive grid layout displaying core tools like the Symptom Checker and Recovery Tracker.

```html
<!-- Mother Dashboard: Feature Grid Snippet -->
<div class="feature-grid">
  <a href="../features/rule_based_mother.html" class="card">
    <div class="icon-box"><i class="fas fa-stethoscope"></i></div>
    <h3>Symptom Checker</h3>
    <p>Check your symptoms quickly with our guide.</p>
  </a>
  
  <a href="../features/recovery_tracker.html" class="card">
    <div class="icon-box"><i class="fas fa-chart-line"></i></div>
    <h3>Recovery Tracker</h3>
    <p>Track your weekly recovery progress.</p>
  </a>
</div>
```

#### B. Professional Dashboard
**Key File**: `dashboards/professional_dashboard.html`
Professionals need quick access to patient data. The dashboard includes a dynamic widget that loads recent symptom logs from `localStorage`.

```javascript
// Professional Dashboard: Loading Patient Logs
const logs = JSON.parse(localStorage.getItem("symptomLogs")) || [];
const logContainer = document.getElementById("symptomLogs");

logs.slice(-3).reverse().forEach(log => {
  const isUrgent = log.status === 'danger';
  const html = `
    <div class="log-card ${isUrgent ? 'urgent' : ''}">
      <span class="patient-name">${log.patientName}</span>
      <span class="log-status">${log.status.toUpperCase()}</span>
      <div class="log-details">Temp: ${log.temp}°C, Pain: ${log.pain}</div>
    </div>
  `;
  logContainer.innerHTML += html;
});
```

#### C. Admin Dashboard
**Key File**: `dashboards/admin_dashboard.html`
The admin view focuses on high-level system metrics. It uses a grid of statistic cards effectively.

```html
<!-- Admin Dashboard: Statistics Snippet -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon primary"><i class="fas fa-users"></i></div>
    <div class="stat-value" id="totalMothers">256</div>
    <div class="stat-label">Total Mothers</div>
  </div>

  <div class="stat-card">
    <div class="stat-icon warning"><i class="fas fa-clock"></i></div>
    <div class="stat-value">12</div>
    <div class="stat-label">Pending Consultations</div>
  </div>
</div>
```

### 4.3.3 Core Feature: Symptom Checker
The Symptom Checker is a rule-based expert system module that evaluates a mother's health inputs to provide immediate guidance.

**Key File**: `features/rule_based_mother.html`
**Implementation Logic**:
The function `checkSymptom()` collects inputs (Temperature, Pain level, Bleeding intensity, Mood) and applies a decision tree to determine the risk level (`Safe`, `Warning`, or `Danger`).

```javascript
// Example logic from rule_based_mother.html
if (temp > 38 || bleeding === 'heavy' || pain === 'severe' || mood === 'very sad') {
    status = 'danger';
    message = "Some symptoms require medical attention. Please consult a doctor immediately.";
} else if (mood === 'sad' || pain === 'yes') {
    status = 'warning';
    message = "Keep an eye on these symptoms. Take rest and stay hydrated.";
} else {
    status = 'safe';
    message = "Your recovery appears to be on track.";
}
```
*Figure 4.1: Logic flow for identifying critical postpartum symptoms.*

### 4.3.4 Recovery Tracker
**Key File**: `features/recovery_tracker.html`
This feature provides a weekly checklist of recovery milestones (e.g., "Week 1: Postnatal Check-In", "Week 2: Pelvic Floor Exercises"). It uses a simple toggle mechanism to mark items as complete, storing the state so progress is saved across sessions.

### 4.3.5 Consultation Management
**Key File**: `features/consultation_management.html`
Enables professionals to view and respond to booking requests. The interface dynamically renders booking cards based on the status (`pending`, `accepted`, `rescheduled`).

```javascript
// Rendering booking cards
bookings.forEach((booking, index) => {
    let statusClass = booking.status; // e.g., 'pending'
    const html = `
        <div class="booking-card ${statusClass}">
            <div class="booking-info">${booking.doctor} - Re: ${booking.patientName}</div>
            <div class="booking-actions">
                <button onclick="acceptBooking(${index})">Accept</button>
            </div>
        </div>`;
    container.innerHTML += html;
});
```

### 4.3.6 Resource Library
**Key File**: `resources/resource_library.html`
A repository of PDF guides and video links. The implementation uses a grid layout (`CSS Grid`) to display resource cards responsively on both mobile and desktop devices.

---

## 4.4 Database Implementation
Although the prototype uses `localStorage`, a robust Relational Database Schema has been designed for the production environment.

**File**: `database/psm_system.sql`
**Schema Overview**:

| Table | Primary Key | Description |
| :--- | :--- | :--- |
| `users` | `id` | Stores Name, Email, Password (hashed), and Role. |
| `recovery_tracking` | `id` | Links to `users`. Stores daily logs (Weight, Mood, Baby Stats). |
| `consultations` | `id` | Connects `mother_id` and `professional_id` with session details. |
| `resources` | `id` | Metadata for uploaded files and external URLs. |

---

## 4.5 Conclusion
The implementation of the PSM System successfully addresses the core requirements of postpartum care management. By combining a responsive frontend with logic-driven features like the Symptom Checker, the system provides a valuable tool for both mothers and healthcare professionals.
