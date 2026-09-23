<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullName = trim($_POST["fullName"]);
    $day = trim($_POST["day"]);
    $month = trim($_POST["month"]);
    $year = trim($_POST["year"]);
    $mobile = trim($_POST["mobile"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $gender = isset($_POST["gender"]) ? $_POST["gender"] : "";

    $department = isset($_POST["department"])
        ? implode(", ", $_POST["department"])
        : "";

    $course = trim($_POST["course"]);
    $city = trim($_POST["city"]);
    $address = trim($_POST["address"]);

    $dob = $day . "/" . $month . "/" . $year;

    if ($fullName == "" || $email == "" || $mobile == "" ||
        $password == "" || $gender == "" || $department == "" ||
        $course == "" || $city == "" || $address == "") {

        echo "<script>alert('Please fill all fields');</script>";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        echo "<script>alert('Invalid email address');</script>";

    } elseif (!preg_match("/^[0-9]{10}$/", $mobile)) {

        echo "<script>alert('Mobile number must be 10 digits');</script>";

    } else {

        $file = fopen("students.csv", "a");

        fputcsv($file, array(
            $fullName,
            $dob,
            $mobile,
            $email,
            $password,
            $gender,
            $department,
            $course,
            $city,
            $address
        ));

        fclose($file);

        echo "<script>
                alert('Your data is successfully stored');
              </script>";
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Student Registration Form</title>
</head>

<body bgcolor="white">
    <table align="center" width="800" cellpadding="10">

        <tr>
            <td colspan="2" align="center">
                <h1>Student Registration Form</h1>
            </td>
        </tr>

        <tr>
            <td>Student Name :</td>
            <td>
                <input type="text"
                       name="fullName"
                       size="60"
                       placeholder="Full Name">
            </td>
        </tr>

        <tr>
            <td>Date Of Birth :</td>
            <td>
                <input type="text" name="day" size="3" placeholder="Day">
                <input type="text" name="month" size="3" placeholder="Month">
                <input type="text" name="year" size="5" placeholder="Year">
            </td>
        </tr>

        <tr>
            <td>Mobile no. :</td>
            <td>
                <input type="text" size="3" value="+91" readonly>
                <input type="text"
                       name="mobile"
                       size="40"
                       placeholder="10 digit mobile number">
            </td>
        </tr>

        <tr>
            <td>Email id :</td>
            <td>
                <input type="text" name="email" size="50">
            </td>
        </tr>

        <tr>
            <td>Password :</td>
            <td>
                <input type="password" name="password" size="50">
            </td>
        </tr>

        <tr>
            <td>Gender :</td>
            <td>
                <input type="radio" name="gender" value="Male"> Male
                <input type="radio" name="gender" value="Female"> Female
            </td>
        </tr>

        <tr>
            <td>Department :</td>
            <td>
                <input type="checkbox" name="department[]" value="CSE"> CSE
                <input type="checkbox" name="department[]" value="CE"> CE
                <input type="checkbox" name="department[]" value="IT"> IT
                <input type="checkbox" name="department[]" value="CL"> CL
                <input type="checkbox" name="department[]" value="EC"> EC
            </td>
        </tr>

        <tr>
            <td>Course :</td>
            <td>
                <select name="course">

                    <option value="">
                        ---------------- Select Course ----------------
                    </option>

                    <option value="Python">Python</option>
                    <option value="Java">Java</option>
                    <option value="DSA">DSA</option>
                    <option value="WDE">WDE</option>
                    <option value="PHP">PHP</option>

                </select>
            </td>
        </tr>

        <tr>
            <td>City :</td>
            <td>
                <input type="text" name="city" size="50">
            </td>
        </tr>

        <tr>
            <td>Address :</td>
            <td>
                <textarea name="address"
                          cols="45"
                          rows="5"></textarea>
            </td>
        </tr>

        <tr>
            <td colspan="2" align="center">

                <input type="submit" value="Register">

            </td>
        </tr>

    </table>

</form>

</body>
</html>
