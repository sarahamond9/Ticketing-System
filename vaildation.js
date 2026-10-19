// Sarah Amond
// C00308719
// Minimal client side check for dynamic fields verification
document.addEventListener("DOMContentLoaded", function() { // Wait for the HTML document to fully load before running script
    const forms = document.querySelectorAll("form"); // Select all form elements on the page
    forms.forEach(form => { // Loop through each form found
        form.addEventListener("submit", function(event) { // Listen for the form submit event
            let valid = true; // Initialize a flag to track validation status
            const inputs = form.querySelectorAll("input[required], textarea[required]"); // Select all required input and textarea elements within the form
            
            inputs.forEach(input => { // Loop through each required field
                if(!input.value.trim()){ // Check if the trimmed input value is empty
                    valid = false; // Mark form as invalid
                    input.style.borderColor = "red"; // Highlight the empty field border in red
                } else {
                    input.style.borderColor = ""; // Reset the border color if the field is filled
                }
            });

            if(!valid){ // Check if any required fields failed validation
                event.preventDefault(); // Stop the form from submitting
                alert("Please complete all required text fields before processing."); // Display an alert warning message to the user
            }
        });
    });
});