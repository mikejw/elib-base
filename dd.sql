



CREATE TABLE user_profile(
id                      INT(11)                 AUTO_INCREMENT PRIMARY KEY,
fullname                VARCHAR(128)            NOT NULL,
picture                 VARCHAR(128)            NULL,
about                   TEXT                    NULL) ENGINE=InnoDB;



CREATE TABLE e_user(
id                      INT(11)                 AUTO_INCREMENT PRIMARY KEY,
user_profile_id         INT(11)                 NOT NULL,
email                   VARCHAR(128)            NOT NULL,
auth                    TINYINT(1)              NOT NULL DEFAULT 0,
username                VARCHAR(8)              NOT NULL,
password                VARCHAR(32)             NOT NULL,
reg_code                VARCHAR(32)             NOT NULL, 
active                  TINYINT(1)              NOT NULL DEFAULT 0,
registered              TIMESTAMP               NULL,
activated               TIMESTAMP               NULL,
FOREIGN KEY (user_profile_id) REFERENCES user_profile(id)) ENGINE=InnoDB;

