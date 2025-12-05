<?php
header("Content-Type: application/json; charset=UTF-8");
require_once 'db_connect.php';

$response = array();
$valid_extensions = array('jpeg', 'jpg', 'png', 'gif');
$upload_path = 'uploads/';

// Create directory if it doesn't exist
if (!file_exists($upload_path)) {
    mkdir($upload_path, 0777, true);
}

// Helper function for validation
function validateInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Handle the request
$action = isset($_POST['action']) ? $_POST['action'] : '';

switch ($action) {
    case 'create':
        // Validation
        $errors = array();

        // Name validation
        $name = isset($_POST['name']) ? validateInput($_POST['name']) : '';
        if (empty($name)) {
            $errors['name'] = 'Name is required';
        } elseif (!preg_match("/^[a-zA-Z ]*$/", $name)) {
            $errors['name'] = 'Only letters and white space allowed';
        }

        // Email validation
        $email = isset($_POST['email']) ? validateInput($_POST['email']) : '';
        if (empty($email)) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        } else {
            // Check if email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $errors['email'] = 'Email already exists';
            }
            $stmt->close();
        }

        // Phone validation
        $phone = isset($_POST['phone']) ? validateInput($_POST['phone']) : '';
        if (empty($phone)) {
            $errors['phone'] = 'Phone is required';
        } elseif (!preg_match("/^[0-9]{10,15}$/", $phone)) {
            $errors['phone'] = 'Invalid phone number';
        }

        // Gender validation
        $gender = isset($_POST['gender']) ? validateInput($_POST['gender']) : '';
        if (!empty($gender) && !in_array($gender, ['male', 'female', 'other'])) {
            $errors['gender'] = 'Invalid gender selection';
        } elseif (empty($gender)) {
            $errors['gender'] = 'gender is required';
        }

        // Interests validation
        $interests = isset($_POST['interests']) ? $_POST['interests'] : array();
        if (!empty($interests)) {
            $interests = is_array($interests) ? implode(',', $interests) : $interests;
        } elseif (empty($interests)) {
            $errors['interests'] = 'Interests is required';
        } else {
            $interests = '';
        }

        // Newsletter validation
        $newsletter = isset($_POST['newsletter']) ? 1 : 0;

        if (empty($newsletter)) {
            $errors['newsletter'] = 'Newsletter is required';
        } else {
            $newsletter = '';
        }

        // Country validation
        $country = isset($_POST['country']) ? validateInput($_POST['country']) : '';
        if (empty($country)) {
            $errors['country'] = 'Country is required';
        } else {
            $country = '';
        }

        // Profile image validation
        $profile_image = '';

        if (empty($errors)) {
            if (isset($_FILES['profile_image']['name']) && !empty($_FILES['profile_image']['name'])) {
                $img = $_FILES['profile_image']['name'];
                $tmp = $_FILES['profile_image']['tmp_name'];
                $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));

                if (in_array($ext, $valid_extensions)) {
                    $profile_image = uniqid() . '.' . $ext;
                    $path = $upload_path . $profile_image;

                    if (!move_uploaded_file($tmp, $path)) {
                        $errors['profile_image'] = 'Failed to upload image';
                    }
                } else {
                    $errors['profile_image'] = 'Invalid file type. Only jpeg, jpg, png, gif allowed';
                }
            }
        } elseif (empty($_FILES['profile_image']['name'])) {
            $errors['profile_image'] = 'Profile Image required!';
        }

        // 3. FINAL VALIDATION - CLEAN UP IF ANY ERRORS OCCURRED
        // ====================================================
        if (!empty($errors)) {
            // If there were errors, delete any uploaded file
            if (!empty($profile_image) && file_exists($upload_path . $profile_image)) {
                unlink($upload_path . $profile_image);
                $profile_image = '';
            }
        }

        if (count($errors) > 0) {
            $response['status'] = 'error';
            $response['errors'] = $errors;
        } else {
            // Insert into database
            $stmt = $conn->prepare("INSERT INTO users (name, email, phone, gender, interests, newsletter, country, profile_image) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssiss", $name, $email, $phone, $gender, $interests, $newsletter, $country, $profile_image);

            if ($stmt->execute()) {
                $response['status'] = 'success';
                $response['message'] = 'User created successfully';
                $response['user_id'] = $stmt->insert_id;
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Failed to create user: ' . $conn->error;
            }
            $stmt->close();
        }
        break;

    case 'read':
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if ($id > 0) {
            // Read single user
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $response['status'] = 'success';
                $response['user'] = $user;
            } else {
                $response['status'] = 'error';
                $response['message'] = 'User not found';
            }
            $stmt->close();
        } else {
            // Read all users
            $result = $conn->query("SELECT * FROM users ORDER BY id DESC");
            $users = array();

            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }

            $response['status'] = 'success';
            $response['users'] = $users;
        }
        break;

    case 'update':
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if ($id <= 0) {
            $response['status'] = 'error';
            $response['message'] = 'Invalid user ID';
            break;
        }

        // Check if user exists
        $stmt = $conn->prepare("SELECT id, profile_image FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            $response['status'] = 'error';
            $response['message'] = 'User not found';
            $stmt->close();
            break;
        }

        $user = $result->fetch_assoc();
        $stmt->close();

        // Validation
        $errors = array();

        // Name validation
        $name = isset($_POST['name']) ? validateInput($_POST['name']) : '';
        if (empty($name)) {
            $errors['name'] = 'Name is required';
        } elseif (!preg_match("/^[a-zA-Z ]*$/", $name)) {
            $errors['name'] = 'Only letters and white space allowed';
        }

        // Email validation
        $email = isset($_POST['email']) ? validateInput($_POST['email']) : '';
        if (empty($email)) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        } else {
            // Check if email already exists for another user
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->bind_param("si", $email, $id);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $errors['email'] = 'Email already exists';
            }
            $stmt->close();
        }

        // Phone validation
        $phone = isset($_POST['phone']) ? validateInput($_POST['phone']) : '';
        if (empty($phone)) {
            $errors['phone'] = 'Phone is required';
        } elseif (!preg_match("/^[0-9]{10,15}$/", $phone)) {
            $errors['phone'] = 'Invalid phone number';
        }

        // Gender validation
        $gender = isset($_POST['gender']) ? validateInput($_POST['gender']) : '';
        if (!empty($gender) && !in_array($gender, ['male', 'female', 'other'])) {
            $errors['gender'] = 'Invalid gender selection';
        }

        // Interests validation
        $interests = isset($_POST['interests']) ? $_POST['interests'] : array();
        if (!empty($interests)) {
            $interests = is_array($interests) ? implode(',', $interests) : $interests;
        } else {
            $interests = '';
        }

        // Newsletter validation
        $newsletter = isset($_POST['newsletter']) ? 1 : 0;

        // Country validation
        $country = isset($_POST['country']) ? validateInput($_POST['country']) : '';

        // Profile image validation
        $profile_image = $user['profile_image'];
        if (isset($_FILES['profile_image']['name']) && !empty($_FILES['profile_image']['name'])) {
            $img = $_FILES['profile_image']['name'];
            $tmp = $_FILES['profile_image']['tmp_name'];
            $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));

            if (in_array($ext, $valid_extensions)) {
                // Delete old image if exists
                if (!empty($profile_image) && file_exists($upload_path . $profile_image)) {
                    unlink($upload_path . $profile_image);
                }

                $profile_image = uniqid() . '.' . $ext;
                $path = $upload_path . $profile_image;

                if (!move_uploaded_file($tmp, $path)) {
                    $errors['profile_image'] = 'Failed to upload image';
                }
            } else {
                $errors['profile_image'] = 'Invalid file type. Only jpeg, jpg, png, gif allowed';
            }
        }

        if (count($errors) > 0) {
            $response['status'] = 'error';
            $response['errors'] = $errors;
        } else {
            // Update database
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, gender = ?, 
                                  interests = ?, newsletter = ?, country = ?, profile_image = ? 
                                  WHERE id = ?");
            $stmt->bind_param(
                "sssssissi",
                $name,
                $email,
                $phone,
                $gender,
                $interests,
                $newsletter,
                $country,
                $profile_image,
                $id
            );

            if ($stmt->execute()) {
                $response['status'] = 'success';
                $response['message'] = 'User updated successfully';
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Failed to update user: ' . $conn->error;
            }
            $stmt->close();
        }
        break;

    case 'delete':
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if ($id <= 0) {
            $response['status'] = 'error';
            $response['message'] = 'Invalid user ID';
            break;
        }

        // First get user to delete profile image
        $stmt = $conn->prepare("SELECT profile_image FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Delete profile image if exists
            if (!empty($user['profile_image']) && file_exists($upload_path . $user['profile_image'])) {
                unlink($upload_path . $user['profile_image']);
            }

            $stmt->close();

            // Delete user from database
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                $response['status'] = 'success';
                $response['message'] = 'User deleted successfully';
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Failed to delete user: ' . $conn->error;
            }
            $stmt->close();
        } else {
            $response['status'] = 'error';
            $response['message'] = 'User not found';
            $stmt->close();
        }
        break;

    default:
        $response['status'] = 'error';
        $response['message'] = 'Invalid action';
}

$conn->close();
echo json_encode($response);
