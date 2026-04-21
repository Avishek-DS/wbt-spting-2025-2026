<?php
$fNameErr = $lNameErr = $genderErr = $emailErr = $companyErr = $reasonErr = $topicsErr = $dateErr = "";
$f_name = $l_name = $gender = $email = $company = $reason = $date = "";
$topics = [];

function cleanInput($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["f_name"])) {
        $fNameErr = "First name is required";
    } else {
        $f_name = cleanInput($_POST["f_name"]);
        if (!preg_match("/^[a-zA-Z-' ]*$/", $f_name)) {
            $fNameErr = "Only letters and white space allowed";
        }
    }

    if (empty($_POST["l_name"])) {
        $lNameErr = "Last name is required";
    } else {
        $l_name = cleanInput($_POST["l_name"]);
        if (!preg_match("/^[a-zA-Z-' ]*$/", $l_name)) {
            $lNameErr = "Only letters and white space allowed";
        }
    }

    if (empty($_POST["gender"])) {
        $genderErr = "Gender is required";
    } else {
        $gender = cleanInput($_POST["gender"]);
    }

    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = cleanInput($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }
    $company = cleanInput($_POST["company"] ?? "");

    if (empty($_POST["reason"])) {
        $reasonErr = "Reason of contact is required";
    } else {
        $reason = cleanInput($_POST["reason"]);
    }

    if (empty($_POST["topics"])) {
        $topicsErr = "At least one topic is required";
    } else {
        foreach ($_POST["topics"] as $topic) {
            $topics[] = cleanInput($topic);
        }
    }

    if (empty($_POST["date"])) {
        $dateErr = "Consultation date is required";
    } else {
        $date = cleanInput($_POST["date"]);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact Me</title>
    <link rel="stylesheet" href="../css/contact.css">
</head>

<body>

    <header class="container">
        <h1>Contact Form</h1>

        <nav class="navbar" aria-label="Primary">
            <a href="../index.html">Home</a>
        </nav>

        <hr />
    </header>

    <main>
        <section aria-labelledby="contact-info">
            <h2 id="contact-info">Contact Information</h2>

            <form method="post" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <fieldset>
                    <legend>Fill up the form</legend>

                    <table>
                        <tr>
                            <td><label for="f_name">First Name:</label></td>
                            <td>
                                <input type="text" id="f_name" name="f_name" placeholder="Enter your First Name" value="<?= $f_name ?>" />
                                <span style="color:red">* <?= $fNameErr ?></span>
                            </td>
                        </tr>

                        <tr>
                            <td><label for="l_name">Last Name:</label></td>
                            <td>
                                <input type="text" id="l_name" name="l_name" placeholder="Enter your Last Name" value="<?= $l_name ?>" />
                                <span style="color:red">* <?= $lNameErr ?></span>
                            </td>
                        </tr>

                        <tr>
                            <td><span>Gender:</span></td>
                            <td>
                                <label>
                                    <input type="radio" name="gender" value="male" <?= ($gender == "male") ? "checked" : "" ?> />
                                    Male
                                </label>

                                <label>
                                    <input type="radio" name="gender" value="female" <?= ($gender == "female") ? "checked" : "" ?> />
                                    Female
                                </label>
                                <span style="color:red">* <?= $genderErr ?></span>
                            </td>
                        </tr>

                        <tr>
                            <td><label for="email">Email:</label></td>
                            <td>
                                <input type="text" id="email" name="email" placeholder="Enter your email" value="<?= $email ?>" />
                                <span style="color:red">* <?= $emailErr ?></span>
                            </td>
                        </tr>

                        <tr>
                            <td><label for="company">Company:</label></td>
                            <td>
                                <input type="text" id="company" name="company" placeholder="Company name" value="<?= $company ?>" />
                                <span style="color:red"><?= $companyErr ?></span>
                            </td>
                        </tr>

                        <tr>
                            <td><label for="reason">Reason of Contact:</label></td>
                            <td>
                                <select id="reason" name="reason">
                                    <option value="">-- Select one --</option>
                                    <option value="projects" <?= ($reason == "projects") ? "selected" : "" ?>>Projects</option>
                                    <option value="thesis" <?= ($reason == "thesis") ? "selected" : "" ?>>Thesis</option>
                                    <option value="job" <?= ($reason == "job") ? "selected" : "" ?>>Job</option>
                                </select>
                                <span style="color:red">* <?= $reasonErr ?></span>
                            </td>
                        </tr>

                        <tr>
                            <td><span>Topics:</span></td>
                            <td>
                                <label>
                                    <input type="checkbox" name="topics[]" value="web development" <?= in_array("web development", $topics) ? "checked" : "" ?> />
                                    Web Development
                                </label>

                                <label>
                                    <input type="checkbox" name="topics[]" value="mobile development" <?= in_array("mobile development", $topics) ? "checked" : "" ?> />
                                    Mobile Development
                                </label>

                                <label>
                                    <input type="checkbox" name="topics[]" value="ai/ml development" <?= in_array("ai/ml development", $topics) ? "checked" : "" ?> />
                                    AI/ML Development
                                </label>
                                <span style="color:red">* <?= $topicsErr ?></span>
                            </td>
                        </tr>

                        <tr>
                            <td><label for="date">Consultation Date:</label></td>
                            <td>
                                <input type="date" id="date" name="date" value="<?= $date ?>" />
                                <span style="color:red">* <?= $dateErr ?></span>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <input type="submit" value="Submit" />
                            </td>
                            <td>
                                <input type="reset" value="Reset" />
                            </td>
                        </tr>
                    </table>

                </fieldset>
            </form>
        </section>
    </main>

    <?php if (
        $_SERVER["REQUEST_METHOD"] == "POST" &&
        !$fNameErr && !$lNameErr && !$genderErr && !$emailErr &&
        !$reasonErr && !$topicsErr && !$dateErr
    ): ?>
        <h3>Submitted values</h3>
        First Name: <?= $f_name ?><br>
        Last Name: <?= $l_name ?><br>
        Gender: <?= $gender ?><br>
        Email: <?= $email ?><br>
        Company: <?= $company ?><br>
        Reason of Contact: <?= $reason ?><br>
        Topics: <?= implode(", ", $topics) ?><br>
        Consultation Date: <?= $date ?><br>
    <?php endif; ?>

    <footer class="container">
        <hr />
        <p>© 2026 Avishek Saha</p>

        <p>Connect with me:</p>

        <p>
            <a href="https://github.com/dosta-yum" target="_blank" rel="noopener noreferrer">
                <img src="../images/github.png" alt="GitHub" width="30" />
            </a>

            &nbsp;

            <a href="https://www.linkedin.com/" target="_blank" rel="noopener noreferrer">
                <img src="../images/linkedin.png" alt="LinkedIn" width="30" />
            </a>
        </p>
    </footer>

</body>

</html>