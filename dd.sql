



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
username                VARCHAR(32)             NOT NULL,
password                VARCHAR(32)             NOT NULL,
reg_code                VARCHAR(32)             NULL,
active                  TINYINT(1)              NOT NULL DEFAULT 0,
registered              TIMESTAMP               NULL,
activated               TIMESTAMP               NULL,
FOREIGN KEY (user_profile_id) REFERENCES user_profile(id)) ENGINE=InnoDB;



CREATE TABLE contact(
id                      INT(11)                 AUTO_INCREMENT PRIMARY KEY,
user_id         		INT(11)                 NULL,
message                 TINYINT(1)              NOT NULL DEFAULT 0,
subject                 VARCHAR(255)            NULL,
body                    TEXT                    NULL,
email                   VARCHAR(255)            NOT NULL,
first_name              VARCHAR(255)            NOT NULL,
last_name               VARCHAR(255)            NOT NULL,
submitted               TIMESTAMP               NULL,
FOREIGN KEY (user_id) REFERENCES e_user(id)) ENGINE=InnoDB;

CREATE TABLE shipping_address(
id     			        INT(11)					AUTO_INCREMENT PRIMARY KEY,
user_id			        INT(11)					NOT NULL,
first_name		        VARCHAR(128)			NULL,
last_name		        VARCHAR(128)			NULL,
address1		        VARCHAR(128)			NULL,
address2		        VARCHAR(128)			NULL,
city			        VARCHAR(128)			NULL,
state			        VARCHAR(128)			NULL,
zip			            VARCHAR(128)			NULL,
country			        VARCHAR(128)			NULL,
default_address		    TINYINT(1)				NOT NULL DEFAULT 0,
FOREIGN KEY (user_id) REFERENCES e_user(id)) ENGINE=InnoDB;

