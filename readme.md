---------------------------------------------
CUSTOM SIDEBAR PLUGIN HOW TO:
---------------------------------------------

1. Install and activate the plugin
2. Add the code below outside your loop in index.php
3. Go to Appearance -> Custom Sidebar
4. Select which post type you want the option to be shown on
5. You're good to go!!


---------------------------------------------
THE CODE:
---------------------------------------------

<?php
 if(function_exists('custom_sidebar')) {
  custom_sidebar('name-1');
 } else {
  dynamic_sidebar('name-1');
 }
?>


---------------------------------------------
NOTE:
---------------------------------------------

custom_sidebar($fallback = 'string')

The functions parameter is the fallback sidebar. If the function doesn't find any
custom sidebar, it will use the fallback sidebar.