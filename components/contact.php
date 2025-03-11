<?php
$formSubmitted = false;
$formError = false;

// Form processing logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Basic validation
    if (
        !empty($_POST['first_name']) && 
        !empty($_POST['last_name']) && 
        !empty($_POST['email']) && 
        !empty($_POST['message'])
    ) {
        // In a real application, you would process the form data here
        // For example, send an email or store in database
        
        // For demonstration, we'll just set a success flag
        $formSubmitted = true;
    } else {
        $formError = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Sign Language Center</title>
    <meta name="description" content="Get in touch with our sign language experts and support team.">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#3b82f6',
                            foreground: '#ffffff',
                        },
                        muted: {
                            DEFAULT: '#f3f4f6',
                            foreground: '#6b7280',
                        },
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-white">
    <div class="container mx-auto px-4 py-12 md:py-16 lg:py-18">
        <div class="mx-auto max-w-5xl space-y-10">

            <?php if ($formSubmitted): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Thank you!</strong>
                <span class="block sm:inline"> Your message has been sent successfully. We'll get back to you soon.</span>
            </div>
            <?php endif; ?>

            <?php if ($formError): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline"> Please fill out all required fields.</span>
            </div>
            <?php endif; ?>

            <div class="grid gap-8 md:grid-cols-2">
                <div class="space-y-6">
                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                        <div class="p-6">
                            <div class="space-y-6">
                                <div class="flex items-start space-x-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-primary shrink-0"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                    <div class="space-y-1">
                                        <h3 class="font-medium">Our Location</h3>
                                        <p class="text-sm text-muted-foreground">123 Sign Language Avenue</p>
                                        <p class="text-sm text-muted-foreground">New York, NY 10001</p>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-primary shrink-0"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                    <div class="space-y-1">
                                        <h3 class="font-medium">Phone</h3>
                                        <p class="text-sm text-muted-foreground">+1 (555) 123-4567</p>
                                        <p class="text-sm text-muted-foreground">
                                            <span class="font-medium">Videophone (VP):</span> +1 (555) 765-4321
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-primary shrink-0"><rect width="20" height="16" x="2" y="4" rx="2"></rect><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path></svg>
                                    <div class="space-y-1">
                                        <h3 class="font-medium">Email</h3>
                                        <p class="text-sm text-muted-foreground">info@signlanguagecenter.com</p>
                                        <p class="text-sm text-muted-foreground">support@signlanguagecenter.com</p>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-primary shrink-0"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                    <div class="space-y-1">
                                        <h3 class="font-medium">Hours</h3>
                                        <p class="text-sm text-muted-foreground">Monday - Friday: 9:00 AM - 6:00 PM</p>
                                        <p class="text-sm text-muted-foreground">Saturday: 10:00 AM - 4:00 PM</p>
                                        <p class="text-sm text-muted-foreground">Sunday: Closed</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                        <div class="p-6">
                            <h3 class="mb-4 font-medium">Connect With Us</h3>
                            <div class="flex space-x-4">
                                <a href="#" class="rounded-full bg-muted p-2 hover:bg-muted/80">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                                    <span class="sr-only">Facebook</span>
                                </a>
                                <a href="#" class="rounded-full bg-muted p-2 hover:bg-muted/80">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"></path></svg>
                                    <span class="sr-only">Twitter</span>
                                </a>
                                <a href="#" class="rounded-full bg-muted p-2 hover:bg-muted/80">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line></svg>
                                    <span class="sr-only">Instagram</span>
                                </a>
                                <a href="#" class="rounded-full bg-muted p-2 hover:bg-muted/80">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17"></path><path d="m10 15 5-3-5-3z"></path></svg>
                                    <span class="sr-only">YouTube</span>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>

                <div>
                    <div class="tabs w-full">
                        <div class="grid w-full grid-cols-2 mb-6">
                            <button class="tab-trigger py-2 font-medium border-b-2 border-primary" data-tab="contact">Contact Form</button>
                            <button class="tab-trigger py-2 font-medium border-b border-gray-200" data-tab="faq">FAQs</button>
                        </div>
                        
                        <div class="tab-content" id="contact-tab">
                            <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                                <div class="p-6">
                                    <form class="space-y-4" method="POST" action="" id="contactForm">
                                        <div class="grid gap-4 sm:grid-cols-2">
                                            <div class="space-y-2">
                                                <label for="first_name" class="text-sm font-medium">
                                                    First name
                                                </label>
                                                <input 
                                                    id="first_name" 
                                                    name="first_name" 
                                                    placeholder="Enter your first name" 
                                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                                />
                                            </div>
                                            <div class="space-y-2">
                                                <label for="last_name" class="text-sm font-medium">
                                                    Last name
                                                </label>
                                                <input 
                                                    id="last_name" 
                                                    name="last_name" 
                                                    placeholder="Enter your last name" 
                                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                                />
                                            </div>
                                        </div>
                                        <div class="space-y-2">
                                            <label for="email" class="text-sm font-medium">
                                                Email
                                            </label>
                                            <input 
                                                id="email" 
                                                name="email" 
                                                type="email" 
                                                placeholder="Enter your email" 
                                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                            />
                                        </div>
                                        <div class="space-y-2">
                                            <label for="subject" class="text-sm font-medium">
                                                Subject
                                            </label>
                                            <input 
                                                id="subject" 
                                                name="subject" 
                                                placeholder="How can we help you?" 
                                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                            />
                                        </div>
                                        <div class="space-y-2">
                                            <label for="message" class="text-sm font-medium">
                                                Message
                                            </label>
                                            <textarea
                                                id="message"
                                                name="message"
                                                placeholder="Please provide details about your inquiry..."
                                                rows="5"
                                                class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                            ></textarea>
                                        </div>
                                        <div class="space-y-2">
                                            <div class="flex items-center space-x-2">
                                                <input
                                                    type="checkbox"
                                                    id="communication_preference"
                                                    name="communication_preference"
                                                    class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary"
                                                />
                                                <label for="communication_preference" class="text-sm text-muted-foreground">
                                                    I prefer video call communication (ASL)
                                                </label>
                                            </div>
                                        </div>
                                        <button 
                                            type="submit" 
                                            class="inline-flex items-center justify-center rounded-md bg-primary text-primary-foreground h-10 px-4 py-2 w-full text-sm font-medium transition-colors hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-1"
                                        >
                                            Send Message
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-content hidden" id="faq-tab">
                            <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                                <div class="p-6 space-y-4">
                                    <div class="space-y-2">
                                        <h3 class="font-medium">Do you offer virtual sign language classes?</h3>
                                        <p class="text-sm text-muted-foreground">
                                            Yes, we offer both in-person and virtual sign language classes for all levels. Our virtual classes are conducted through secure video conferencing platforms.
                                        </p>
                                    </div>
                                    <div class="space-y-2">
                                        <h3 class="font-medium">What sign language dialects do you teach?</h3>
                                        <p class="text-sm text-muted-foreground">
                                            We primarily teach American Sign Language (ASL), but also offer courses in British Sign Language (BSL) and International Sign.
                                        </p>
                                    </div>
                                    <div class="space-y-2">
                                        <h3 class="font-medium">Do you provide interpreting services?</h3>
                                        <p class="text-sm text-muted-foreground">
                                            Yes, we offer professional sign language interpreting services for events, meetings, medical appointments, and more. Please contact us for rates and availability.
                                        </p>
                                    </div>
                                    <div class="space-y-2">
                                        <h3 class="font-medium">How can I access learning resources?</h3>
                                        <p class="text-sm text-muted-foreground">
                                            All registered students have access to our online learning portal with videos, practice exercises, and supplementary materials. We also have a physical resource library at our center.
                                        </p>
                                    </div>
                                    <button 
                                        class="inline-flex items-center justify-center rounded-md border border-input bg-background h-10 px-4 py-2 w-full text-sm font-medium shadow-sm transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-1 mt-4"
                                    >
                                        View All FAQs
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Simple tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabTriggers = document.querySelectorAll('.tab-trigger');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabTriggers.forEach(trigger => {
                trigger.addEventListener('click', function() {
                    // Reset all tabs
                    tabTriggers.forEach(t => {
                        t.classList.remove('border-primary');
                        t.classList.add('border-gray-200');
                    });
                    
                    // Hide all content
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                    });
                    
                    // Activate clicked tab
                    this.classList.add('border-primary');
                    this.classList.remove('border-gray-200');
                    
                    // Show corresponding content
                    const tabId = this.getAttribute('data-tab');
                    document.getElementById(tabId + '-tab').classList.remove('hidden');
                });
            });
            
            // Form validation
            const contactForm = document.getElementById('contactForm');
            if (contactForm) {
                contactForm.addEventListener('submit', function(e) {
                    let isValid = true;
                    const requiredFields = ['first_name', 'last_name', 'email', 'message'];
                    
                    requiredFields.forEach(field => {
                        const input = document.getElementById(field);
                        if (!input.value.trim()) {
                            isValid = false;
                            input.classList.add('border-red-500');
                        } else {
                            input.classList.remove('border-red-500');
                        }
                    });
                    
                    // Email validation
                    const emailInput = document.getElementById('email');
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(emailInput.value)) {
                        isValid = false;
                        emailInput.classList.add('border-red-500');
                    }
                    
                    if (!isValid) {
                        e.preventDefault();
                        alert('Please fill out all required fields correctly.');
                    }
                });
            }
        });
    </script>
</body>
</html>

