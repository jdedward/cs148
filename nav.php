<nav>
     <ol>
<?php 
if(basename($_SERVER['PHP_SELF'])=="home.php"){
    print '<li class="activePage">Home</li>' . "\n";
} else {
    print '<li><a href="home.php">Home</a></li>' . "\n";
} 

if(basename($_SERVER['PHP_SELF'])=="register.php"){
    print '<li class="activePage">Register</li>' . "\n";
} else {
    print '<li><a href="register.php">Register</a></li>' . "\n";
} 

if(basename($_SERVER['PHP_SELF'])=="order.php"){
    print '<li class="activePage">Order</li>' . "\n";
} else {
    print '<li><a href="order.php">Order</a></li>' . "\n";
} 

if(basename($_SERVER['PHP_SELF'])=="admin.php"){
    print '<li class="activePage">Admin</li>' . "\n";
} else {
    print '<li><a href="admin.php">Admin</a></li>' . "\n";
} 




// repeat above for each menu option
?>   
     </ol>
</nav>