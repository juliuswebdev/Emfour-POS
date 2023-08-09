<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('superadmin_frontend_pages', function (Blueprint $table) {
            //
        });

        //HRIS module setting in system table
        \DB::table('superadmin_frontend_pages')->insert(
            array(
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => '<p>Emfour Technology Solutions, LLC is committed to protecting your privacy, and that includes protecting the privacy of any data you share with us. This policy describes what data we collect, what we do with your data, when and how we store your data, and how it is shared.&nbsp;</p>

                <p><strong>Data Collection</strong>.<br />
                We collect data from you through the use of pages on this website and interaction with our products and services. Sometimes the data we collect relates to you or identifies you. Data always includes personal data. Our goal is always to maintain the highest levels of privacy and security with your personal data, in keeping with the principles of the Federal Trade Commission Act and related regulations, the General Data Protection Regulation, and applicable local privacy laws. We will always try to give you appropriate notice of what data we collect and how we will use it, and we will only process your data when we consider it fair and lawful to do so.&nbsp;</p>
                
                <p>We may collect data from you on records of correspondence between us and you (for example, records of your and our communication for purposes of support services); and information you provide by completing forms on our website or in our products and services (for example, purchase information or contacting us).&nbsp;</p>
                
                <p>Sometimes the reason for collection will be when you give us your email address to allow us to contact you. When it is not, we will describe to you at the time of collection the purpose for collecting the data and if possible, ask for your consent. To the extent possible, the data we collect from you are presented anonymously.&nbsp;</p>
                
                <p>In general, we do not intentionally collect sensitive personal data. However, if you provide us with sensitive personal data, whether through the website or through any of our products or services, you explicitly consent us to our use and process that data.&nbsp;</p>
                
                <p>&nbsp;</p>
                
                <p><strong>Data Usage.</strong></p>
                
                <p>We use the data we collect from you to provide information, services, or products you have requested or purchased, and to allow you to interact with us and the website. This may include sending you emails from time to time; these emails always contain either information you have requested or that you have consented to receive, or information we have a legitimate interest in delivering (such as notices of product changes).&nbsp;&nbsp;</p>
                
                <p>&nbsp;</p>
                
                <p><strong>Keeping Your Data.</strong></p>
                
                <p>In general, we keep your data only as long as necessary to provide the service or product you requested. If you are a customer who has an account with us, we will keep the data connected to your account until you ask us to destroy it. If your account is inactive, your data will only be stored so that the account can be reactivated in the future, and your data will not be processed for any other purpose. You may request us to destroy your data at any time by following the procedures outlined in this policy.&nbsp;</p>
                
                <p>Your data is stored in the cloud through the Amazon Web Services.&nbsp;</p>
                
                <p>&nbsp;</p>
                
                <p><strong>Sharing Data</strong>.</p>
                
                <p>We never share any of your data with anyone. Our system may contain links to business partners or other third parties. Please note that those websites have their own policies, and we do not accept any responsibility or liability for your use of those websites or any products or services available there.&nbsp;</p>
                
                <p>&nbsp;</p>
                
                <p><strong>Personal data.</strong></p>
                
                <p>You have a right to know if we have any of your personal data and to have access to that data, and you have the right to have any incorrect personal data corrected. If you have given us consent to have or use your data, you have the right to withdraw that consent at any time. You also have the right to have your personal data erased.&nbsp;</p>
                
                <p>Understand that sometimes we have to have your data in order to interact with you, and so exercising some or all of these rights might impact your ability to use our website or our products and services. You may contact us at any time, to exercise any of these rights.&nbsp;</p>
                
                <p>If you have any additional questions about our privacy practices, please contact us.&nbsp;</p>
                
                <p>&nbsp;</p>',
                'is_shown' => 1,
                'menu_order' => 1
            )
        );

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('superadmin_frontend_pages', function (Blueprint $table) {
            //
        });
    }
};
