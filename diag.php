<?php
echo "<pre>";
echo "user: ".get_current_user()." uid: ".getmyuid()."\n";
echo "disable_functions: ".ini_get('disable_functions')."\n";
echo "shell_exec available: ".(function_exists('shell_exec')?'yes':'no')."\n";
passthru('/usr/bin/ffmpeg -version 2>&1', $ret);
echo "ffmpeg exit code: $ret\n";
