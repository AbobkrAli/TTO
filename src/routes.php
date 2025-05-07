// Supervisor routes
$router->get('/supervisor/departments', 'SupervisorController@departments');
$router->get('/supervisor/departments/view/{id}', 'SupervisorController@viewDepartment');
$router->post('/supervisor/departments/{id}/optional-subjects', 'SupervisorController@addOptionalSubject');
$router->post('/supervisor/departments/{departmentId}/optional-subjects/{subjectId}',
'SupervisorController@deleteOptionalSubject');