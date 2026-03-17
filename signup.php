<?php
include("config/db.php");

if(isset($_POST['register'])){

$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$role = $_POST['role'];

$sql = "INSERT INTO users(name,email,password,role)
VALUES('$name','$email','$password','$role')";

$conn->query($sql);

header("Location: login.php");

}
?>

<?php include("includes/navbar.php"); ?>

<main class="main-container" style="display: flex; justify-content: center; align-items: center; min-height: 60vh;">
    <div class="glass-card" style="width: 100%; max-width: 400px; padding: 2.5rem;">
        <div class="section-header" style="text-align: center; margin-bottom: 2rem;">
            <h2 style="font-size: 1.75rem; font-weight: 700;">Signup</h2>
            <p style="color: var(--text-secondary); margin-top: 0.5rem;">Create a new account</p>
        </div>
        <form method="POST">
            <input type="text" name="name" placeholder="Name" required style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--card-border); border-radius: 8px; background: transparent; color: var(--text-primary); margin-bottom: 1rem; transition: border-color 0.2s;">
            <input type="email" name="email" placeholder="Email" required style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--card-border); border-radius: 8px; background: transparent; color: var(--text-primary); margin-bottom: 1rem; transition: border-color 0.2s;">
            <input type="password" name="password" placeholder="Password" required style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--card-border); border-radius: 8px; background: transparent; color: var(--text-primary); margin-bottom: 1rem; transition: border-color 0.2s;">
            
            <select name="role" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--card-border); border-radius: 8px; background: var(--bg-color); color: var(--text-primary); margin-bottom: 1.5rem; transition: border-color 0.2s;">
                <option value="public">Public User</option>
                <option value="station">Station User</option>
            </select>
            
            <button class="btn btn-primary" name="register" style="width: 100%; justify-content: center; padding: 0.75rem; margin-bottom: 1.5rem;">Register</button>
            <div style="text-align: center; color: var(--text-secondary); font-size: 0.9rem;">
                Already have an account? <a href="login.php" style="color: var(--primary); text-decoration: none; font-weight: 500;">Login here</a>
            </div>
        </form>
    </div>
</main>
<?php include("includes/footer.php"); ?>