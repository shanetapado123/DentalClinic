<?php 






?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quitaneg's Dental Clinic</title>
    <link rel="stylesheet" href="style.css">
</head>
<style type="text/css">
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', 'Segoe UI', sans-serif;
    }
    
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #333;
        overflow-x: hidden;
    }
    
    header {
        background: linear-gradient(to right, #2c3e50, #4a6491);
        padding: 1.2rem 0;
        position: sticky;
        top: 0;
        z-index: 1000;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }
    
    nav ul {
        display: flex;
        justify-content: center;
        list-style: none;
        flex-wrap: wrap;
    }
    
    nav ul li {
        margin: 0 12px;
        position: relative;
    }
    
    nav ul li a {
        color: white;
        text-decoration: none;
        padding: 12px 22px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        display: inline-block;
        letter-spacing: 0.5px;
        text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    }
    
    /* Hover Effects */
    nav ul li a:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }
    
    /* Underline animation */
    nav ul li a::before {
        content: '';
        position: absolute;
        width: 0;
        height: 3px;
        background: linear-gradient(to right, #f8a5c2, #f5cd79);
        bottom: 5px;
        left: 50%;
        transform: translateX(-50%);
        transition: width 0.4s ease, opacity 0.3s ease;
        opacity: 0;
        border-radius: 3px;
    }
    
    nav ul li a:hover::before {
        width: 60%;
        opacity: 1;
    }
    
    /* Active link style */
    nav ul li a.active {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    nav ul li a.active::before {
        width: 60%;
        opacity: 1;
    }
    
    nav ul li a.res {
        background: linear-gradient(45deg, #ff6b6b, #ff8e8e);
        box-shadow: 0 4px 15px rgba(255, 107, 107, 0.4);
    }
    
    nav ul li a.res:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(255, 107, 107, 0.5);
    }
    
    /* Hero section */
    .hero {
        background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.)), 
                    url('2.jpg');
        background-size: cover;
        background-position: center;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .hero::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 100px;
        background: linear-gradient(transparent, #f5f7fa);
    }
    
    .hero-content h1 {
        font-size: 4rem;
        margin-bottom: 1.5rem;
        text-shadow: 0 3px 6px rgba(0,0,0,0.3);
        animation: fadeInDown 1s ease;
    }
    
    .hero-content p {
        font-size: 1.5rem;
        margin-bottom: 2.5rem;
        max-width: 700px;
        margin-left: auto;
        margin-right: auto;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        animation: fadeInUp 1s ease;
    }
    
    .button {
        display: inline-block;
        padding: 15px 30px;
        background: linear-gradient(45deg, #3498db, #2ecc71);
        color: white;
        text-decoration: none;
        border-radius: 50px;
        font-weight: 600;
        margin: 0 10px;
        transition: all 0.4s ease;
        box-shadow: 0 4px 15px rgba(46, 204, 113, 0.4);
        animation: fadeIn 1.5s ease;
    }
    
    .button:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(46, 204, 113, 0.6);
        background: linear-gradient(45deg, #2ecc71, #3498db);
    }
    
    /* About section */
    .about {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 80px 0;
        color: white;
        position: relative;
    }
    
    .about::before {
        content: '';
        position: absolute;
        top: -50px;
        left: 0;
        width: 100%;
        height: 50px;
        
    }
    
    .service-item img {
        border-radius: 15px;
        box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        transition: transform 0.5s ease;
        margin-bottom: 30px;
    }
    
    .service-item img:hover {
        transform: scale(1.03) rotate(-1deg);
    }
    
    /* Services section */
    #services {
        background: #f5f7fa;
        padding: 80px 0;
    }
    
    .service-list {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 40px;
    }
    
    .service-item {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        transition: transform 0.4s ease, box-shadow 0.4s ease;
        max-width: 600px;
    }
    
    .service-item:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.12);
    }
    
    .service-item h3 {
        color: #2c3e50;
        margin-bottom: 15px;
        font-size: 1.8rem;
    }
    
    .service-item ul {
        list-style-type: none;
    }
    
    .service-item ul li {
        padding: 8px 0;
        position: relative;
        padding-left: 25px;
    }
    
    .service-item ul li::before {
        content: '✓';
        position: absolute;
        left: 0;
        color: #2ecc71;
        font-weight: bold;
    }
    
    /* Contact section */
    .contact {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        padding: 80px 0;
        position: relative;
    }
    
    .contact::before {
        content: '';
        position: absolute;
        top: -50px;
        left: 0;
        width: 100%;
        height: 50px;
        background: linear-gradient(to bottom left, transparent 49%, #f5f7fa 50%);
    }
    
    #contactForm {
        max-width: 600px;
        margin: 0 auto;
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }
    
    #contactForm label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #2c3e50;
    }
    
    #contactForm input,
    #contactForm textarea {
        width: 100%;
        padding: 12px 15px;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    #contactForm input:focus,
    #contactForm textarea:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        outline: none;
    }
    
    #contactForm textarea {
        min-height: 150px;
        resize: vertical;
    }
    
    #contactForm button {
        background: linear-gradient(45deg, #3498db, #2ecc71);
        color: white;
        border: none;
        padding: 15px 30px;
        font-size: 1rem;
        font-weight: 600;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.4s ease;
        display: block;
        margin: 0 auto;
        box-shadow: 0 4px 15px rgba(46, 204, 113, 0.4);
    }
    
    #contactForm button:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(46, 204, 113, 0.5);
    }
    
    /* Footer */
    footer {
        background: #2c3e50;
        color: white;
        text-align: center;
        padding: 25px 0;
        font-size: 1rem;
    }
    
    /* Admin activator */
    .admin-activator {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        transition: all 0.3s ease;
        z-index: 1000;
    }
    
    
    /* Animations */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        nav ul {
            flex-direction: column;
            align-items: center;
        }
        
        nav ul li {
            margin: 10px 0;
        }
        
        .hero-content h1 {
            font-size: 2.5rem;
        }
        
        .hero-content p {
            font-size: 1.2rem;
        }
        
        .button {
            display: block;
            margin: 15px auto;
            max-width: 200px;
        }
        
        .service-item {
            padding: 20px;
        }
    }
</style>
<body>
     <header>
        <nav>
            <ul>
                <li><a href="#home" class="active">Home</a></li>
                <li><a href="#about">About Us</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </nav>
    </header>

        
           


    <section id="home" class="hero">
        <div class="hero-content">
            <h1>Quitaneg's Dental Clinic</h1>
            <p>Your smile is our priority. Providing exceptional dental care. sign up to set an appointment now!</p><br>
            <a href="login.php" class="button">Sign In</a>
            <a href="register.php" class="button">Sign Up</a>
        </div>
    </section>

        <center>
    <section id="about" class="about">
        <div class="service-item">
            
            <h2 style="color: black;">About Us</h2>
            <img src="bg/1.jpg"style="width: 550px; height: 550px;">
            <p style="color: black; font-size: 20px; font-family: monospace;"><mark>Quitaneg Dental Clinic </mark>is dedicated to providing high-quality dental care in a comfortable and friendly environment.<br>Our team of experienced dentists and staff are committed to helping you achieve and maintain a healthy, beautiful smile.<br>
            We offer a wide range of services, including general dentistry, cosmetic dentistry, orthodontics, and more.<br>We use the latest technology and techniques to ensure that you receive the best possible care.</p>

        </div>
    </section>
    </center>

    <section id="services">
        <div class="">
            <h2 style="color: black;">Our Dental Services</h2>
            <div class="service-list">
                <div class="service-item">
                    <img src="bg/3.jpg"  style="width: 550px; height: 550px;">
                   
                    <ul style="color: black">
                        <li>Orthodontics (Braces)</li>
                        <li>Craniodontic TMJ</li>
                        <li>Esthetic Dentistry</li>
                        <li>Oral Surgery (Removal of Impacted 3rd Molar)</li>
                        <li>Consultation</li>
                        <li>Restorative Management (Pasta)</li>
                        <li>Prosthodontics (Dentures,Fixed Bridge,Jacket Crown)</li>
                        <li>Oral Prophylaxis (Cleaning)</li>
                    </ul>
                </div>
                <div class="service-item">
                    <img src="bg/4.jpg" style="width: 550px; height: 550px;">
                   
                    <ul style="color: black">
                        <li>Pit and Fissure sealant</li>
                        <li>Stainless Steel Crown</li>
                        <li>Strip of Crown</li>
                        <li>Tooth Extraction</li>
                        <li>Restorative Management</li>
                        <li>Oral Prophylaxis</li>
                        <li>Topical Barnish Application</li>
                        <li>Pediatric Dentistry</li>
                    </ul>
                </div>
             </section>

     <section id="contact" class="contact">
        <div class="">
             <img src="bg/5.jpg" style="width: 350px; height: 350px;">
            <h2 style="color: black;">Contact Us</h2>
            <p style="color: black;">Zone 2 Aypa, IBA Zambales Beside Metrobank IBA</p>
            <form id="contactForm" action="process_contact.php" method="POST">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required><br><br>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required><br><br>

                <label for="message">Message:</label>
                <textarea id="message" name="message" required></textarea><br><br>

                <button type="submit">Send Message</button>
            </form>
            <div id="messageDisplay"></div>
        </div>
    </section>

    <footer>
        <p>&copy; 2025  Quitaneg's Dental Clinic. All rights reserved.</p>
    </footer>
     <script>
        // Add active class to current nav item
        document.querySelectorAll('nav ul li a').forEach(link => {
            link.addEventListener('click', function() {
                document.querySelectorAll('nav ul li a').forEach(item => {
                    item.classList.remove('active');
                });
                this.classList.add('active');
            });
        });
    </script>
    <script src="script.js"></script>
    <!-- Hidden admin activator (click in this area to activate) -->
    <div class="admin-activator" onclick="activateAdminMode()"></div>
    <script>
function activateAdminMode() {
    const confirmed = confirm("ADMIN LOGIN");

    if (confirmed) {
        // Redirect to admin login page
        window.location.href = "admin_login.php";
    }
}
</script>
</body>
</html>