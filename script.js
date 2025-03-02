document.getElementById("signupForm").addEventListener("submit", function(event) {
    event.preventDefault();

    let name = document.getElementById("name").value;
    let email = document.getElementById("email").value;
    let password = document.getElementById("password").value;

    if (name === "" || email === "" || password === "") {
        document.getElementById("statusMessage").innerText = "All fields are required!";
        return;
    }

    // Simulating success
    document.getElementById("statusMessage").innerText = "✅ Sign-up successful!";
});
