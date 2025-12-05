<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Operations with jQuery AJAX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .error {
            color: red;
            font-size: 0.9em;
        }

        .profile-img-preview {
            max-width: 150px;
            max-height: 150px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">User Management System</h2>

        <!-- User Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h4 id="formTitle">Add New User</h4>
            </div>
            <div class="card-body">
                <form id="userForm" enctype="multipart/form-data">
                    <input type="hidden" id="userId" name="id" value="0">
                    <input type="hidden" name="action" id="formAction" value="create">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name*</label>
                                <input type="text" class="form-control" id="name" name="name">
                                <div class="error" id="nameError"></div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email*</label>
                                <input type="email" class="form-control" id="email" name="email">
                                <div class="error" id="emailError"></div>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone*</label>
                                <input type="text" class="form-control" id="phone" name="phone">
                                <div class="error" id="phoneError"></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Gender</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gender" id="male" value="male">
                                        <label class="form-check-label" for="male">Male</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gender" id="female" value="female">
                                        <label class="form-check-label" for="female">Female</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gender" id="other" value="other">
                                        <label class="form-check-label" for="other">Other</label>
                                    </div>
                                </div>
                                <div class="error" id="genderError"></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Interests</label>
                                <div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="interests[]" id="sports" value="sports">
                                        <label class="form-check-label" for="sports">Sports</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="interests[]" id="music" value="music">
                                        <label class="form-check-label" for="music">Music</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="interests[]" id="reading" value="reading">
                                        <label class="form-check-label" for="reading">Reading</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="interests[]" id="travel" value="travel">
                                        <label class="form-check-label" for="travel">Travel</label>
                                    </div>
                                </div>
                                <div class="error" id="interestsError"></div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter" value="1">
                                    <label class="form-check-label" for="newsletter">Subscribe to newsletter</label>
                                </div>
                                <div class="error" id="newsletterError"></div>
                            </div>

                            <div class="mb-3">
                                <label for="country" class="form-label">Country</label>
                                <select class="form-select" id="country" name="country">
                                    <option value="">Select Country</option>
                                    <option value="USA">United States</option>
                                    <option value="UK">United Kingdom</option>
                                    <option value="Canada">Canada</option>
                                    <option value="Australia">Australia</option>
                                    <option value="India">India</option>
                                </select>
                                <div class="error" id="countryError"></div>
                            </div>

                            <div class="mb-3">
                                <label for="profile_image" class="form-label">Profile Image</label>
                                <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*">
                                <div class="error" id="profile_imageError"></div>
                                <img id="profileImagePreview" class="profile-img-preview d-none">


                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="button" class="btn btn-secondary me-md-2" id="resetBtn">Reset</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">Submit</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Users Table -->
        <div class="card">
            <div class="card-header">
                <h4>User List</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Gender</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody">
                            <!-- Users will be loaded here via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- View User Modal -->
    <div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewUserModalLabel">User Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="userDetails">
                    <!-- User details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Load users on page load
            loadUsers();

            // Form submission
            $('#userForm').on('submit', function(e) {
                e.preventDefault();
                submitForm();
            });

            // Reset form
            $('#resetBtn').click(function() {
                resetForm();
            });

            // Preview profile image before upload
            $('#profile_image').change(function() {
                previewImage(this);
            });
        });

        function loadUsers() {
            $.ajax({
                url: 'api.php',
                type: 'POST',
                data: {
                    action: 'read'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        let html = '';
                        if (response.users && response.users.length > 0) {
                            response.users.forEach(function(user) {
                                html += `
                                    <tr>
                                        <td>${user.id}</td>
                                        <td>${user.name}</td>
                                        <td>${user.email}</td>
                                        <td>${user.phone}</td>
                                        <td>${user.gender ? user.gender.charAt(0).toUpperCase() + user.gender.slice(1) : ''}</td>
                                        <td>
                                            <button class="btn btn-sm btn-info view-btn" data-id="${user.id}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning edit-btn" data-id="${user.id}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-btn" data-id="${user.id}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                `;
                            });
                        } else {
                            html = '<tr><td colspan="6" class="text-center">No users found</td></tr>';
                        }
                        $('#userTableBody').html(html);

                        // Attach event handlers to the new buttons
                        attachEventHandlers();
                    } else {
                        alert('Error loading users: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('AJAX Error: ' + error);
                }
            });
        }

        function attachEventHandlers() {
            // View user
            $('.view-btn').click(function() {
                const userId = $(this).data('id');
                viewUser(userId);
            });

            // Edit user
            $('.edit-btn').click(function() {
                const userId = $(this).data('id');
                editUser(userId);
            });

            // Delete user
            $('.delete-btn').click(function() {
                const userId = $(this).data('id');
                if (confirm('Are you sure you want to delete this user?')) {
                    deleteUser(userId);
                }
            });
        }

        function viewUser(userId) {
            $.ajax({
                url: 'api.php',
                type: 'POST',
                data: {
                    action: 'read',
                    id: userId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success' && response.user) {
                        const user = response.user;
                        let html = `
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    ${user.profile_image ? 
                                        `<img src="uploads/${user.profile_image}" class="img-fluid rounded mb-3" alt="Profile Image">` : 
                                        '<p>No image available</p>'}
                                </div>
                                <div class="col-md-8">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>ID</th>
                                            <td>${user.id}</td>
                                        </tr>
                                        <tr>
                                            <th>Name</th>
                                            <td>${user.name}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>${user.email}</td>
                                        </tr>
                                        <tr>
                                            <th>Phone</th>
                                            <td>${user.phone}</td>
                                        </tr>
                                        <tr>
                                            <th>Gender</th>
                                            <td>${user.gender ? user.gender.charAt(0).toUpperCase() + user.gender.slice(1) : ''}</td>
                                        </tr>
                                        <tr>
                                            <th>Interests</th>
                                            <td>${user.interests ? user.interests.split(',').join(', ') : 'None'}</td>
                                        </tr>
                                        <tr>
                                            <th>Newsletter</th>
                                            <td>${user.newsletter == 1 ? 'Subscribed' : 'Not Subscribed'}</td>
                                        </tr>
                                        <tr>
                                            <th>Country</th>
                                            <td>${user.country || 'Not specified'}</td>
                                        </tr>
                                        <tr>
                                            <th>Created At</th>
                                            <td>${user.created_at}</td>
                                        </tr>
                                        <tr>
                                            <th>Updated At</th>
                                            <td>${user.updated_at}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        `;
                        $('#userDetails').html(html);
                        $('#viewUserModal').modal('show');
                    } else {
                        alert('Error loading user: ' + (response.message || 'User not found'));
                    }
                },
                error: function(xhr, status, error) {
                    alert('AJAX Error: ' + error);
                }
            });
        }

        function editUser(userId) {
            $.ajax({
                url: 'api.php',
                type: 'POST',
                data: {
                    action: 'read',
                    id: userId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success' && response.user) {
                        const user = response.user;

                        // Set form to edit mode
                        $('#formTitle').text('Edit User');
                        $('#formAction').val('update');
                        $('#userId').val(user.id);
                        $('#submitBtn').text('Update');

                        // Fill form fields
                        $('#name').val(user.name);
                        $('#email').val(user.email);
                        $('#phone').val(user.phone);

                        // Set gender
                        $('input[name="gender"]').prop('checked', false);
                        if (user.gender) {
                            $(`input[name="gender"][value="${user.gender}"]`).prop('checked', true);
                        }

                        // Set interests
                        $('input[name="interests[]"]').prop('checked', false);
                        if (user.interests) {
                            const interests = user.interests.split(',');
                            interests.forEach(function(interest) {
                                $(`input[name="interests[]"][value="${interest}"]`).prop('checked', true);
                            });
                        }

                        // Set newsletter
                        $('#newsletter').prop('checked', user.newsletter == 1);

                        // Set country
                        $('#country').val(user.country);

                        // Set profile image preview
                        if (user.profile_image) {
                            $('#profileImagePreview')
                                .attr('src', 'uploads/' + user.profile_image)
                                .removeClass('d-none');
                        } else {
                            $('#profileImagePreview').addClass('d-none');
                        }

                        // Scroll to form
                        $('html, body').animate({
                            scrollTop: $('#userForm').offset().top
                        }, 500);
                    } else {
                        alert('Error loading user for edit: ' + (response.message || 'User not found'));
                    }
                },
                error: function(xhr, status, error) {
                    alert('AJAX Error: ' + error);
                }
            });
        }

        function deleteUser(userId) {
            $.ajax({
                url: 'api.php',
                type: 'POST',
                data: {
                    action: 'delete',
                    id: userId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert('User deleted successfully');
                        loadUsers();
                    } else {
                        alert('Error deleting user: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('AJAX Error: ' + error);
                }
            });
        }

        function submitForm() {
            // Clear previous errors
            $('.error').text('');

            // Create FormData object to handle file upload
            const formData = new FormData($('#userForm')[0]);

            console.log(formData);

            $.ajax({
                url: 'api.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        resetForm();
                        loadUsers();
                    } else if (response.status === 'error' && response.errors) {
                        // Display validation errors
                        for (const field in response.errors) {
                            $(`#${field}Error`).text(response.errors[field]);

                        }
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('AJAX Error: ' + error);
                }
            });
        }

        function resetForm() {
            $('#userForm')[0].reset();
            $('#formTitle').text('Add New User');
            $('#formAction').val('create');
            $('#userId').val('0');
            $('#submitBtn').text('Submit');
            $('.error').text('');
            $('#profileImagePreview').addClass('d-none');
        }

        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    $('#profileImagePreview')
                        .attr('src', e.target.result)
                        .removeClass('d-none');
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>

</html>