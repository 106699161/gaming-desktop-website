<?php include("header.inc"); ?>

<!-- ================= PAYMENT BANNER ================= -->

<section class="banner">

    <h1>Gaming Desktop Payment</h1>

    <p>
        Complete the form below to purchase your selected gaming desktop.
        Your order will be checked by the server before it is stored.
    </p>

</section>


<!-- ================= PAYMENT FORM ================= -->

<div class="container">

    <h2>Gaming Desktop Checkout</h2>

    <!--
        HTML5 client-side validation is intentionally disabled.
        Assignment Part 2 requires server-side validation in
        process_order.php.
    -->

    <form action="process_order.php" method="post" novalidate>

        <!-- ================= CUSTOMER DETAILS ================= -->

        <fieldset>

            <legend>Customer Details</legend>

            <label for="fname">First Name</label>
            <input
                type="text"
                id="fname"
                name="fname"
                maxlength="25"
                pattern="[A-Za-z ]{1,25}"
                required
                placeholder="Enter First Name"
            >

            <label for="lname">Last Name</label>
            <input
                type="text"
                id="lname"
                name="lname"
                maxlength="25"
                pattern="[A-Za-z ]{1,25}"
                required
                placeholder="Enter Last Name"
            >

            <label for="email">Email Address</label>
            <input
                type="email"
                id="email"
                name="email"
                required
                placeholder="example@gmail.com"
            >

            <label for="phone">Phone Number</label>
            <input
                type="tel"
                id="phone"
                name="phone"
                maxlength="10"
                pattern="[0-9]{10}"
                required
                placeholder="0712345678"
            >

            <label for="contact">Preferred Contact</label>

            <select id="contact" name="contact" required>
                <option value="">Select Preferred Contact</option>
                <option value="Email">Email</option>
                <option value="Phone">Phone</option>
                <option value="Post">Post</option>
            </select>

        </fieldset>


        <!-- ================= ADDRESS ================= -->

        <fieldset>

            <legend>Delivery Address</legend>

            <label for="str_addr">Street Address</label>
            <input
                type="text"
                id="str_addr"
                name="str_addr"
                maxlength="40"
                required
                placeholder="Street Address"
            >

            <label for="suburb">Suburb / Town</label>
            <input
                type="text"
                id="suburb"
                name="suburb"
                maxlength="20"
                pattern="[A-Za-z0-9 .'-]{1,20}"
                required
                placeholder="Suburb / Town"
            >

            <label for="state">State</label>

            <select id="state" name="state" required>
                <option value="">Select State</option>
                <option value="VIC">VIC</option>
                <option value="NSW">NSW</option>
                <option value="QLD">QLD</option>
                <option value="NT">NT</option>
                <option value="WA">WA</option>
                <option value="SA">SA</option>
                <option value="TAS">TAS</option>
                <option value="ACT">ACT</option>
            </select>

            <label for="pcode">Postcode</label>

            <input
                type="text"
                id="pcode"
                name="pcode"
                maxlength="4"
                pattern="[0-9]{4}"
                required
                placeholder="1234"
            >

        </fieldset>


        <!-- ================= PRODUCT DETAILS ================= -->

        <fieldset>

            <legend>Product Details</legend>

            <label for="product">Select Product</label>

            <select id="product" name="product" required>
                <option value="">Choose Gaming PC</option>

                <option value="ASUS ROG STRIX">
                    ASUS ROG STRIX - Rs.1,000,000
                </option>

                <option value="MSI Infinite RS">
                    MSI Infinite RS - Rs.850,000
                </option>

                <option value="Alienware Aurora">
                    Alienware Aurora - Rs.450,000
                </option>

                <option value="Corsair Vengeance">
                    Corsair Vengeance - Rs.700,000
                </option>

                <option value="HP Omen">
                    HP Omen - Rs.650,000
                </option>

                <option value="Lenovo Legion">
                    Lenovo Legion - Rs.680,000
                </option>

                <option value="NZXT Player One">
                    NZXT Player One - Rs.590,000
                </option>

                <option value="Custom RGB Gaming PC">
                    Custom RGB Gaming PC - Rs.1,250,000
                </option>

            </select>


            <label for="qty">Quantity</label>

            <input
                type="number"
                id="qty"
                name="qty"
                min="1"
                max="10"
                value="1"
                required
            >


            <label>Additional Product Options</label>

            <div class="check-group">

                <label>
                    <input
                        type="checkbox"
                        name="options[]"
                        value="RTX Graphics"
                    >
                    RTX Graphics (+Rs.50,000)
                </label>

                <label>
                    <input
                        type="checkbox"
                        name="options[]"
                        value="RGB Lighting"
                    >
                    RGB Lighting (+Rs.15,000)
                </label>

                <label>
                    <input
                        type="checkbox"
                        name="options[]"
                        value="Liquid Cooling"
                    >
                    Liquid Cooling (+Rs.30,000)
                </label>

                <label>
                    <input
                        type="checkbox"
                        name="options[]"
                        value="DDR5 RAM"
                    >
                    DDR5 RAM (+Rs.25,000)
                </label>

                <label>
                    <input
                        type="checkbox"
                        name="options[]"
                        value="SSD Storage"
                    >
                    SSD Storage (+Rs.20,000)
                </label>

                <label>
                    <input
                        type="checkbox"
                        name="options[]"
                        value="WiFi 6"
                    >
                    WiFi 6 (+Rs.10,000)
                </label>

            </div>

        </fieldset>


        <!-- ================= CREDIT CARD DETAILS ================= -->

        <fieldset>

            <legend>Credit Card Details</legend>

            <label for="card_type">Credit Card Type</label>

            <select id="card_type" name="card_type" required>

                <!-- No valid default selection -->

                <option value="">Select Card Type</option>

                <option value="Visa">Visa</option>

                <option value="Mastercard">
                    Mastercard
                </option>

                <option value="American Express">
                    American Express
                </option>

            </select>


            <label for="card_name">Name on Credit Card</label>

            <input
                type="text"
                id="card_name"
                name="card_name"
                maxlength="40"
                required
                placeholder="Name on Card"
            >


            <label for="card_number">Credit Card Number</label>

            <input
                type="text"
                id="card_number"
                name="card_number"
                maxlength="16"
                pattern="[0-9]{15,16}"
                required
                placeholder="Enter card number"
                autocomplete="off"
                inputmode="numeric"
            >


            <label for="expiry">Card Expiry Date (MM-YY)</label>

            <input
                type="text"
                id="expiry"
                name="expiry"
                maxlength="5"
                pattern="(0[1-9]|1[0-2])-[0-9]{2}"
                required
                placeholder="MM-YY"
                autocomplete="off"
            >


            <label for="cvv">Card Verification Value (CVV)</label>

            <input
                type="text"
                id="cvv"
                name="cvv"
                maxlength="4"
                pattern="[0-9]{3,4}"
                required
                placeholder="CVV"
                autocomplete="off"
                inputmode="numeric"
            >

        </fieldset>


        <!-- ================= COMMENTS ================= -->

        <fieldset>

            <legend>Additional Information</legend>

            <label for="comments">Comments</label>

            <textarea
                id="comments"
                name="comments"
                rows="6"
                placeholder="Enter any additional requirements..."
            ></textarea>

        </fieldset>


        <!-- ================= BUTTONS ================= -->

        <div class="buttons">

            <button type="submit">
                Check Out
            </button>

            <button type="reset">
                Reset
            </button>

        </div>

    </form>

</div>


<?php include("footer.inc"); ?>