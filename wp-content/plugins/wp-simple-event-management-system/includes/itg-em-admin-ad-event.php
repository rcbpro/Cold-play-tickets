<?php
if ('itg-em-admin-ad-event.php' == basename($_SERVER['SCRIPT_FILENAME']))
    die ('<h2>Direct File Access Prohibited</h2>');
if(!current_user_can('itg_em_cap_admin')) {
    wp_die('Cheating eh? No luck if Swashata is the developer ;)');
    return;
}
?>
<div class="wrap">
    <?php
    /**
     * Add the event
     * if the req method was post
     * use $wpdb->insert
     */
    global $itg_em_db_table_name, $wpdb, $itg_em_options;
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        /** Strip slash */
        if ( get_magic_quotes_gpc() ) {
            $_POST = array_map( 'stripslashes_deep', $_POST );
        }
        
        /** First check of any error */
        $error = '';
        if((int)$_POST['team_mem'] <= 0) {
            $error .= '<p>Team member can not be 0. It has been updated to 1</p>';
            $_POST['team_mem'] = 1;
        }
        if((int)$_POST['mem_op'] != 0 && (int)$_POST['mem_op'] != 1) {
            $error .= '<p>Member Allocation data tampered. It has been set to Optional</p>';
            $_POST['mem_op'] = 0;
        }
        if($_POST['round'] <= 0) {
            $error .= '<p>Number of rounds can not be 0. It has been set to 1</p>';
            $_POST['round'] = 1;
        }
        
        /**
         * Set the price
         */
        if('cus' == $_POST['price']) {
            $_POST['price'] = (float) $_POST['price_cus'];
        }
        if($_POST['price'] < 0 && $_POST['price'] != -1.00) {
            $_POST['price'] = -1*$_POST['price'];
            $error .= '<p>The price cannot be negative. The positive value has been used.</p>';
        }
        
        /**
         * Set the date
         */
        $start_date_raw = strtotime($_POST['start_date']);
        $start_date = date('Y-m-d', $start_date_raw);
        
        $end_date_raw = strtotime($_POST['end_date']);
        $end_date = date('Y-m-d', $end_date_raw);
        /** Validate */
        if($end_date_raw < $start_date_raw) {
            $end_date = $start_date;
            $error .= '<p>End date is earlier than start date! This is humanly not possible. The developer being human, has decided to make end_date = start_date in such cases!</p>';
        }
        
        /** Now insert the value to the table */
        $wpdb->insert($itg_em_db_table_name['admin_event'], array(
            'event_name' => $_POST['event_name'],
            'event_desc' => $_POST['event_desc'],
            'start_date' => $start_date,
            'end_date' => $end_date,
            'team_mem' => $_POST['team_mem'],
            'mem_op' => $_POST['mem_op'],
            'round' => $_POST['round'],
            'price' => $_POST['price'],
            'per_disc' => $_POST['per_disc'],
            'venue' => $_POST['venue'],
            'ref' => $_POST['ref']
        ), array(
            '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%f', '%d', '%s', '%s'
        ));
        
        /** Output it */
        if('' != $error) {
            ?>
            <div class="error fade">
                Following errors occured:
                <?php echo $error; ?>
            </div>
            <?php
        }
        ?>
        <div class="updated fade">
            Successfully added the event. You may add another one from the form below. <a href="admin.php?page=itg_event_admin_edit&pmode=edit&item_id=<?php echo $wpdb->insert_id; ?>" class="button-secondary">Edit Event</a>
        </div>
        <?php
    }
    ?>
    <?php
    wp_tiny_mce(false, array(
        "editor_selector" => "my_tiny_mce"
    ));
    ?>  
    <h2>Add a new Event</h2>
    <form action="" method="post">
        <fieldset>
            <legend>Event Details</legend>
            <ul>
                <li>
                    <label for="event_name">Event Name:</label>
                    <input type="text" name="event_name" id="event_name" value="Enter event name" />
                </li>
                <li>
                    <label for="event_desc">Event Description:</label>
                    <textarea name="event_desc" id="event_desc" class="my_tiny_mce"></textarea>
                </li>
                <li>
                    <label for="team_mem">Team Members:</label>
                    <input type="text" name="team_mem" id="team_mem" value="2" />
                </li>
                <li>
                    <label for="mem_op">Member Allocation:</label>
                    <select name="mem_op" id="mem_op">
                        <option value="0" selected="selected">Optional</option>
                        <option value="1">Mandatory</option>
                    </select>
                </li>
                <li>
                    <label for="round">Number of Rounds:</label>
                    <input type="text" name="round" id="round" value="1" />
                </li>
                <li>
                    <label for="price">Price of the Event:</label>
                    <select name="price" id="price">
                        <option value="-1.00" selected="selected">To be announced later</option>
                        <option value="0.00">Free Event</option>
                        <option value="cus">Custom</option>
                    </select>
                    <label for="price_cus">Enter Custom Price:</label>
                    <input type="text" name="price_cus" id="price_cus" value="10.00" /> <?php echo $itg_em_options['currency']; ?>
                </li>
                <li>
                    <label for="start_date">Start Date:</label>
                    <input type="text" name="start_date" id="start_date" class="date_field" value="YYYY-MM-DD" />
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    <label for="end_date">End Date:</label>
                    <input type="text" name="end_date" id="end_date" class="date_field" value="YYYY-MM-DD" />
                </li>
                <li>
                    <label for="per_disc">Discount on bulk registration: </label>
                    <input type="text" name="per_disc" id="per_disc" value="0" /> - percent
                </li>
                <li>
                    <label for="venue">Venue: </label>
                    <textarea id="venue" name="venue" class="my_tiny_mce"></textarea>
                </li>
                <li>
                    <label for="ref">Reference URL:</label>
                    <input type="text" id="ref" name="ref" value="Link to the Post" />
                </li>
                <li>
                    <input type="submit" class="button-primary" value="Add the Event" />
                </li>
            </ul>
        </fieldset>
    </form>
</div>