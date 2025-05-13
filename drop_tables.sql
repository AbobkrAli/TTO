-- Disable foreign key checks temporarily
SET FOREIGN_KEY_CHECKS = 0;

-- Drop the specified tables if they exist
DROP TABLE IF EXISTS `books`;

DROP TABLE IF EXISTS `fines`;

DROP TABLE IF EXISTS `reservation`;

DROP TABLE IF EXISTS `roles`;

DROP TABLE IF EXISTS `transactions`;

DROP TABLE IF EXISTS `users_roles`;

DROP TABLE IF EXISTS `users`;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;