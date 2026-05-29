<?php
require __DIR__ . "../vendor/autoload.php";

use App\Model\Activitycms;

$activityModel = new Activitycms();

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['id'])) {
    $registrationId = $_GET['id'];
    $registrationData = $activityModel->getRegisteredUserById($registrationId);

    if ($registrationData) {
        // Display the registration data in a form for editing
?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Edit Registration</title>
            <!-- Include your CSS stylesheets or link to external CSS files here -->
        </head>

        <body>
            <h1>Edit Registration</h1>
            <form action="process_edit_registration.php" method="POST">
            <?= csrf_field() ?>
                <label for="firstname">First Name:</label>
                <input type="text" id="firstname" name="firstname" value="<?php echo isset($_POST['firstname']) ? htmlspecialchars($_POST['firstname']) : htmlspecialchars($registrationData['firstname'] ?? ''); ?>"><br>

                <label for="lastname">Last Name:</label>
                <input type="text" id="lastname" name="lastname" value="<?php echo isset($_POST['lastname']) ? htmlspecialchars($_POST['lastname']) : htmlspecialchars($registrationData['lastname'] ?? ''); ?>"><br>

                <!-- Add more input fields for other registration data -->
                <!-- Example: -->
                <label for="email">Email:</label>
                <input type="text" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : htmlspecialchars($registrationData['email'] ?? ''); ?>"><br>

                <!-- Display other registration data fields here -->

                <input type="hidden" name="id" value="<?php echo $registrationId; ?>">
                <input type="submit" value="Save">
            </form>
        </body>

        </html>
<?php
    } else {
        echo "Registration not found.";
    }
} else {
    echo "Invalid request.";
}
?>
