# SesameOnline
Anonymous storage your temporary files online.

# Usage

- create database (enable event_scheduler)

```

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


```

- edit /app/config/DB.php

```

<?php

$sesameOl_DB = array();

$sesameOl_DB['host'] = 'localhost:3306';
$sesameOl_DB['username'] = 'root';
$sesameOl_DB['password'] = 'root';
$sesameOl_DB['db'] = 'sesame_ol';

```

- RUN NOW!

# Credits

- [animate.css](https://github.com/daneden/animate.css) 
- [bootstrap](https://github.com/twbs/bootstrap)
- [tailwindcss](https://github.com/tailwindcss/tailwindcss)
- icons from [iconfont](https://www.iconfont.cn) website
- [jquery](https://github.com/jquery/jquery)
- [bootstrap-notify](https://github.com/mouse0270/bootstrap-notify)

# LICENSE

MIT License

Copyright (c) 2018 石固

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
