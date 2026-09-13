<?php include("header.inc"); ?>

<!-- ================= ENHANCEMENTS BANNER ================= -->

<section class="banner">

    <h1>Website Enhancements</h1>

    <p>
        Explore the advanced features implemented in our gaming
        desktop website, including interactive design, responsive
        layouts, modern enquiry forms, and cloud hosting using
        Microsoft Azure.
    </p>

</section>


<!-- ================= ENHANCEMENT CARDS ================= -->

<section class="enhancements">


    <!-- Enhancement 1 -->

    <div class="card">

        <img src="images/enhancment1.png"
             alt="Animated Hero Banner">

        <h2>Animated Hero Banner</h2>

        <p>
            The homepage includes a full-screen hero banner with
            a dark overlay and smooth fade-up animation for text.
        </p>

        <a href="index.php">
            View Home Page
        </a>

    </div>


    <!-- Enhancement 2 -->

    <div class="card">

        <img src="images/enhancment2.png"
             alt="Interactive Product Cards">

        <h2>Interactive Product Cards</h2>

        <p>
            Gaming desktop cards include hover animations,
            RGB glow effects and responsive layouts.
        </p>

        <a href="product.php">
            View Products
        </a>

    </div>


    <!-- Enhancement 3 -->

    <div class="card">

        <img src="images/enhancment3.png"
             alt="Modern Enquiry Form">

        <h2>Modern Enquiry Form</h2>

        <p>
            A responsive enquiry page with HTML5 validation,
            custom styling and gaming-themed design.
        </p>

        <a href="payment.php">
            View Payment
        </a>

    </div>


    <!-- Enhancement 4 -->

    <div class="card">

        <img src="images/enhancment4.jpeg"
             alt="Responsive Mobile View">

        <h2>Mobile View</h2>

        <p>
            The website is responsive and adjusts to mobile screens,
            providing a simple, user-friendly, and easy-to-navigate
            layout.
        </p>

        <a href="index.php">
            View Home Page
        </a>

    </div>


    <!-- Enhancement 5 -->

    <div class="card">

        <img src="images/enhancment5.png"
             alt="Azure Web Hosting">

        <h2>Azure Web Hosting</h2>

        <p>
            The website is deployed and hosted using GitHub and
            Microsoft Azure, allowing users to access the website
            online with secure, reliable, and high-performance hosting.
        </p>

        <a href="https://purple-tree-03ee2c000.7.azurestaticapps.net/"
           target="_blank">
            Visit Live Website
        </a>

    </div>


</section>


<!-- ================= NEW PHP/MYSQL ENHANCEMENTS ================= -->

<section class="enhancements">

    <div class="card">

        <h2>Manager Authentication and Session Security</h2>

        <p>
            A manager authentication feature has been added to protect
            the order management area. The manager must log in before
            accessing the manager page. PHP sessions are used to keep
            track of the authenticated manager during the session.
        </p>

        <p>
            This enhancement improves the security of the website by
            preventing unauthorised users from directly accessing
            sensitive order information and management functions.
        </p>

        <p>
            The implementation uses PHP sessions, server-side
            authentication and a logout function. The manager page
            checks whether the required session variable exists before
            allowing access.
        </p>

        <a href="manager.php">
            View Manager Page
        </a>

    </div>


    <div class="card">

        <h2>Advanced Order and Sales Reports</h2>

        <p>
            The manager area is extended with database-driven reports
            based on the stored customer orders. The reports provide
            useful information about the number of orders, order
            status and sales performance.
        </p>

        <p>
            SQL aggregate functions such as COUNT() and SUM(), together
            with GROUP BY and ORDER BY, can be used to calculate
            statistics from the order data stored in MySQL.
        </p>

        <p>
            This enhancement gives the manager a clearer overview of
            business activity and demonstrates additional PHP and
            MySQL database functionality beyond the basic required
            order table.
        </p>

        <a href="manager.php">
            View Manager Reports
        </a>

    </div>

</section>




<!-- ================= REFERENCES ================= -->

<section class="references">

    <h2>References</h2>

<ol>

        <li>
            W3Schools, <em>"HTML Video"</em>. Available:
            <a href="https://www.w3schools.com/html/html5_video.asp" target="_blank">
                https://www.w3schools.com/html/html5_video.asp
            </a>
        </li>

        <li>
            MDN Web Docs, <em>"HTML &lt;video&gt; Element"</em>. Available:
            <a href="https://developer.mozilla.org/en-US/docs/Web/HTML/Element/video" target="_blank">
                https://developer.mozilla.org/en-US/docs/Web/HTML/Element/video
            </a>
        </li>

        <li>
            W3Schools, <em>"CSS Transitions"</em>. Available:
            <a href="https://www.w3schools.com/css/css3_transitions.asp" target="_blank">
                https://www.w3schools.com/css/css3_transitions.asp
            </a>
        </li>

        <li>
            W3Schools, <em>"HTML Forms"</em>. Available:
            <a href="https://www.w3schools.com/html/html_forms.asp" target="_blank">
                https://www.w3schools.com/html/html_forms.asp
            </a>
        </li>

        <li>
            W3Schools, <em>"Responsive Web Design"</em>. Available:
            <a href="https://www.w3schools.com/css/css_rwd_intro.asp" target="_blank">
                https://www.w3schools.com/css/css_rwd_intro.asp
            </a>
        </li>

        <li>
            Microsoft Learn, <em>"Azure Static Web Apps"</em>. Available:
            <a href="https://learn.microsoft.com/azure/static-web-apps/" target="_blank">
                https://learn.microsoft.com/azure/static-web-apps/
            </a>
        </li>

        <li>
            GitHub Docs. Available:
            <a href="https://docs.github.com/" target="_blank">
                https://docs.github.com/
            </a>
        </li>

        <li>
            Swinburne University of Technology,
            <em>"COS10032 Computing Systems Project - Lecture Slides"</em>,
            Semester 1, 2026. Course material available via Canvas.
       </li>

    </ol>


</section>


<!-- ================= ADDITIONAL FEATURES ================= -->

<section class="features">

    <h2>Additional Features</h2>

    <div class="feature-box">

        <div>
            <i class="fa-solid fa-check"></i>
            Responsive Layout
        </div>

        <div>
            <i class="fa-solid fa-check"></i>
            Smooth CSS Animations
        </div>

        <div>
            <i class="fa-solid fa-check"></i>
            Dark RGB Gaming Theme
        </div>

        <div>
            <i class="fa-solid fa-check"></i>
            Hover Effects
        </div>

        <div>
            <i class="fa-solid fa-check"></i>
            Modern Navigation Bar
        </div>

        <div>
            <i class="fa-solid fa-check"></i>
            HTML5 Form Validation
        </div>

    </div>

</section>


<?php include("footer.inc"); ?>