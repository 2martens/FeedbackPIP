/**** tables ****/
DROP TABLE IF EXISTS wcf1_feedback;
CREATE TABLE wcf1_feedback (
    packageID INT(10) NOT NULL PRIMARY KEY,
    email VARCHAR(255) NOT NULL DEFAULT '',
    subject VARCHAR(255) NOT NULL DEFAULT '',
    userEmailOptional INT(2) NOT NULL DEFAULT '1'
);