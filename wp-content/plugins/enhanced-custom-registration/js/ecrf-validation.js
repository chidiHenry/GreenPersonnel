// JavaScript for Enhanced Custom Registration Form Validation
jQuery(document).ready(function ($) {
    $("#enhanced-registration-form").on("submit", function (e) {
        let valid = true;
        let errorMessage = "";

        // Check if passwords match
        const password = $("input[name='user_pass']").val();
        const confirmPassword = $("input[name='confirm_pass']").val();
        if (password !== confirmPassword) {
            valid = false;
            errorMessage += "Passwords do not match.\n";
        }

        // Check for required file uploads
        if (!$("input[name='cv']").val()) {
            valid = false;
            errorMessage += "Please upload your CV.\n";
        }

        const certificates = $("input[name='certificates[]']")[0].files;
        if (certificates.length > 3) {
            valid = false;
            errorMessage += "You can only upload a maximum of 3 certificates.\n";
        }

        // Show error message if validation fails
        if (!valid) {
            alert(errorMessage);
            e.preventDefault();
        }
    });
});
