-- sesame_ol
CREATE DATABASE IF NOT EXISTS `sesame_ol`;
USE `sesame_ol`;

CREATE TABLE IF NOT EXISTS `mapping` (
  `ticket` varchar(12) NOT NULL COMMENT 'used to match current row',
  `token` varchar(255) NOT NULL COMMENT 'used to restrict access current row',
  `fileID` varchar(15) NOT NULL COMMENT 'remote file ID',
  `url` varchar(80) NOT NULL COMMENT 'remote file download page url',
  `realUrl` varchar(100) NOT NULL DEFAULT '' COMMENT 'remote file real download link',
  `createTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'creation time',
  PRIMARY KEY (`ticket`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE EVENT if NOT EXISTS auto_delete_evt 
 ON SCHEDULE EVERY 1 DAY
   STARTS DATE_ADD(DATE(CURDATE() + 1), INTERVAL 3 HOUR)
 DO 
   DELETE FROM mapping WHERE UNIX_TIMESTAMP(CURRENT_TIMESTAMP()) > UNIX_TIMESTAMP(createTime) + 10 * 24 * 60 * 60;
