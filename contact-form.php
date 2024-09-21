<?php
/**
 * Plugin Name: Contact Form
 * Description: A simple and effective form which can be used for the contact-us pages in any website.
 * Version: 1.0
 * Author: Aastha Anand
 * Author URI: aasthaanand193@hotmail.com
 */

//  Security
 if(!defined('ABSPATH')){
    die();
 }


//  Class
if(!class_exists('contactForm')){
    class contactForm{
        public function __construct()
        {
        }
        public function enqueues(){
            wp_enqueue_style( "fonts", "https://fonts.googleapis.com/css2?family=Fredericka+the+Great&display=swap", array(), 1, "all" );
            wp_enqueue_style("contact-form-style",plugin_dir_url(__FILE__)."contact-form.css",array(),1,"all");
            wp_enqueue_script("contact-form-js",plugin_dir_url(__FILE__)."js/form-submission.js",array(),1,true);
            wp_localize_script("contact-form-js", "wpApiSettings", array(
                'root' => esc_url_raw(rest_url()),
                'nonce' => wp_create_nonce('wp_rest')
            ));
        }
        public function initialize(){
            add_action('wp_enqueue_scripts', [$this,"enqueues"]);
            add_shortcode("contact-form", [$this,"load_contact_form"]);
            //register the post and save the data entered through the form in that post type, non writable configuration, only viewable
            include_once(plugin_dir_path( __FILE__ ).'includes/contact-post-type.php');
        }
        public function load_contact_form(){
             return 
                '<form action="'.plugin_dir_url(__FILE__).'form-submissions-api/submit/'.'" method="post" class="contact-form" autocomplete="off">
                    <div>
                    <label for="name">Name</label>
                    <br>
                    <input type="text" placeholder="Please enter your name: " name="name" class="name-input">
                    </div>
                    <div>
                    <label for="email">Email*</label>
                    <br>
                    <input type="email" name="email" id="email" placeholder="Please enter your email id:" class="email-input" required> 
                    </div>
                    <div>
                    <label for="query">Query/Feedback/Suggestions!</label>
                    <br>
                    <textarea name="query" id="query" placeholder="Type away!" class="textarea-input"></textarea>
                    </div>
                    <button type="submit">Submit</button>
                </form>';
            
            }
    }
    $form=new contactForm;
    $form->initialize();
}
?>
