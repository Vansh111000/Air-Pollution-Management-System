<?php
session_start();
include("config/db.php");

if(isset($_POST['login'])){

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE email='$email'";

$result = $conn->query($sql);

if($result->num_rows > 0){

$user = $result->fetch_assoc();

if(password_verify($password,$user['password'])){

$_SESSION['user_id'] = $user['id'];
$_SESSION['role'] = $user['role'];

if($user['role']=="admin"){
header("Location: admin/dashboard.php");
}

elseif($user['role']=="station"){
header("Location: station/dashboard.php");
}

else{
header("Location: public_dashboard.php");
}

}

}

}
?>

<?php include("includes/navbar.php"); ?>

<main class="main-container" style="display: flex; justify-content: center; align-items: center; min-height: 60vh;">
    <div class="glass-card" style="width: 100%; max-width: 400px; padding: 2.5rem;">
        <div class="section-header" style="text-align: center; margin-bottom: 2rem;">
            <h2 style="font-size: 1.75rem; font-weight: 700;">Login</h2>
            <p style="color: var(--text-secondary); margin-top: 0.5rem;">Access your dashboard</p>
        </div>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--card-border); border-radius: 8px; background: transparent; color: var(--text-primary); margin-bottom: 1rem; transition: border-color 0.2s;">
            <input type="password" name="password" placeholder="Password" required style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--card-border); border-radius: 8px; background: transparent; color: var(--text-primary); margin-bottom: 1.5rem; transition: border-color 0.2s;">
            <button class="btn btn-primary" name="login" style="width: 100%; justify-content: center; padding: 0.75rem; margin-bottom: 1.5rem;">Login</button>
            <div style="text-align: center; color: var(--text-secondary); font-size: 0.9rem;">
                Don't have an account? <a href="signup.php" style="color: var(--primary); text-decoration: none; font-weight: 500;">Sign up</a>
            </div>
        </form>
    </div>
</main>
<?php include("includes/footer.php"); ?>