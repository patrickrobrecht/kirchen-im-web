/* Run this SQL code to initialize the database. */

CREATE TABLE `churches`
(
  `id`           INT(11)      NOT NULL AUTO_INCREMENT,
  `slug`         VARCHAR(100) NULL     DEFAULT NULL,
  `name`         TEXT         NULL,
  `street`       TEXT         NULL,
  `postalCode`   VARCHAR(10)  NULL     DEFAULT NULL,
  `city`         TEXT         NULL,
  `country`      TEXT         NULL,
  `lat`          DOUBLE       NULL     DEFAULT NULL,
  `lon`          DOUBLE       NULL     DEFAULT NULL,
  `denomination` TEXT         NULL,
  `type`         TEXT         NULL,
  `parentId`     INT(11)      NULL     DEFAULT NULL,
  `hasChildren`  INT(1)       NOT NULL DEFAULT '0',
  `timestamp`    DATETIME     NULL     DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `slug` (`slug`)
);

CREATE TABLE `followers`
(
  `followerId` INT(11)  NOT NULL AUTO_INCREMENT,
  `websiteId`  INT(11)  NOT NULL,
  `followers`  INT(11)  NOT NULL,
  `date`       DATETIME NOT NULL,
  PRIMARY KEY (`followerId`),
  INDEX `FK_followers_websites` (`websiteId`),
  CONSTRAINT `FK_followers_websites` FOREIGN KEY (`websiteId`) REFERENCES `websites` (`websiteId`)
);

CREATE TABLE `settings`
(
  `settingsId` INT(11)     NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(50) NULL DEFAULT NULL,
  `value`      TEXT        NULL,
  PRIMARY KEY (`settingsId`),
  UNIQUE INDEX `key` (`name`)
);

CREATE TABLE `websites`
(
  `websiteId`           INT(11)     NOT NULL AUTO_INCREMENT,
  `churchId`            INT(11)     NOT NULL,
  `type`                VARCHAR(20) NOT NULL,
  `url`                 TEXT        NULL,
  `followers`           INT(11)     NULL DEFAULT NULL,
  `followersStatus`     INT(11)     NULL DEFAULT NULL,
  `followersLastUpdate` DATETIME    NULL DEFAULT NULL,
  `statusCode`          INT(11)     NULL DEFAULT NULL,
  `redirectTarget`      TEXT        NULL,
  `lastCheck`           DATETIME    NULL DEFAULT NULL,
  `notes`               TEXT        NULL,
  `notesUpdate`         DATETIME    NULL DEFAULT NULL,
  PRIMARY KEY (`websiteId`),
  INDEX `FK_websites_churches` (`churchId`),
  CONSTRAINT `FK_websites_churches` FOREIGN KEY (`churchId`) REFERENCES `churches` (`id`)
);
