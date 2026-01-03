============================================================
PSM SYSTEM - SYSTEM WALKTHROUGH GUIDELINE
============================================================

This document provides a step-by-step guide to testing and 
demonstrating the full functionality of the PSM System.

------------------------------------------------------------
1. ACCESSING THE SYSTEM
------------------------------------------------------------
- Development URL: http://localhost/psm_system
- Production URL: [Your Vercel App URL]
- Entry Point: index.php (Auto-redirects to Login page)

------------------------------------------------------------
2. USER ROLE: MOTHER (Main User)
------------------------------------------------------------
- Registration: Use 'register.php' to create a new Mother account.
- Login: Log in with Mother credentials.
- Dashboard: 
  * View recovery progress.
  * Access high-level summaries.
- Symptom Checker: 
  * Navigate to Symptom Checker.
  * Complete the screening to see recommendations.
- Consultations:
  * Book a session with a professional.
  * View booking status (Pending/Accepted).
- Resources:
  * Browse Expert Articles.
  * Access Nutrition/Exercise plans.
- Journal:
  * Record daily thoughts or symptoms.

------------------------------------------------------------
3. USER ROLE: PROFESSIONAL (Doctor/Expert)
------------------------------------------------------------
- Dashboard: 
  * Manage assigned patient/mother lists.
  * View upcoming consultations.
- Consultation Management:
  * Accept or Reschedule booking requests from mothers.
- Resource Library:
  * Download professional resources or templates.

------------------------------------------------------------
4. USER ROLE: ADMIN (System Management)
------------------------------------------------------------
- Dashboard:
  * View system-wide analytics (User engagement, trends).
- User Management:
  * Oversee all user accounts (Mother, Professional).
- Content Management:
  * Manage articles and system resources.

------------------------------------------------------------
5. DATABASE VERIFICATION
------------------------------------------------------------
- Run 'check_db.php' to verify the database connection 
  and list existing users.
- Check 'database/psm_system.sql' for the underlying schema.

------------------------------------------------------------
6. DEPLOYMENT CHECKS
------------------------------------------------------------
- Ensure '.htaccess' is working for proper URL routing.
- Verify environment variables (MYSQLHOST, etc.) are 
  configured in Vercel.
- Check Railway console for live database updates.

============================================================
Document Ends
============================================================
