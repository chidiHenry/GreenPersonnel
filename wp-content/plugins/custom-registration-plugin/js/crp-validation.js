// JavaScript for Custom Registration Form Validation
jQuery(document).ready(function ($) {
    $("#custom-registration-form").on("submit", function (e) {
        let valid = true;
        let errorMessage = "";

        // Check if passwords match
        const password = $("input[name='password']").val();
        const confirmPassword = $("input[name='confirm_password']").val();
        if (password !== confirmPassword) {
            valid = false;
            errorMessage += "Passwords do not match.\n";
        }

        // Check required fields
        $("#custom-registration-form input[required], #custom-registration-form textarea[required]").each(function () {
            if ($(this).val().trim() === "") {
                valid = false;
                errorMessage += "Please fill in the required fields.\n";
                return false; // Break loop
            }
        });

        if (!valid) {
            alert(errorMessage);
            e.preventDefault();
        }
    });
});
