**********************************************
**
**  JomSocial Overview
**
**********************************************

* Introduction
**********************************************
Thank you for purchasing JomSocial, the best 
social networking extension available 
exclusively on Joomla! CMS. 

For a Joomla extension, JomSocial is 
considerably large given the scope that it 
has to cater in providing you with the most
cost-effective and easy-to-use social 
networking solution. 

This guide will provide you with a quick 
overview of our system requirement, basic
configuration and our support policy.

**********************************************
** OFFICIAL DOCUMENTATION
** http://www.jomsocial.com/support/docs
**********************************************

* Before installing JomSocial
**********************************************
Global System Requirement:
/ Video upload
/ Photo Upload
/ Facebook Connect 
/ Large photo/video upload 
http://www.jomsocial.com/support/docs/item/725-system-requirement.html


* Installing JomSocial
**********************************************
How to install the component:
http://www.jomsocial.com/support/docs/item/730-installing-jomsocial.html

How to install the modules, applications & plugins:
http://www.jomsocial.com/support/docs/item/717-installing-applications.html

How to configure the plugins:
http://www.jomsocial.com/support/docs/item/750-configuring-applications-and-plugins.html

Setting up cron job / maintenance script:
http://www.jomsocial.com/support/docs/item/720-setting-up-cron-job-scheduled-task.html


* Upgrading to JomSocial 2.6
**********************************************
Upgrading from JomSocial 2.4 and below:
http://www.jomsocial.com/support/docs/item/829-upgrading.html

Language file updates:
http://www.jomsocial.com/support/docs/item/1077-language-file-updates.html

Advanced users - Database changes in JomSocial 2.6:
http://www.jomsocial.com/support/docs/item/1073-database-updates-in-major-jomsocial-versions.html

Special note for JomSocial 2.6 Alpha/Beta/RC users:
There are a couple of performance optimizations
that we have implemented since the last RC3.
Please run this query to update your database
tables (assuming your prefix is jos_#):

ALTER TABLE `jos_community_events` 
ADD KEY `idx_catid` (`catid`),
ADD KEY `idx_published` (`published`);

ALTER TABLE `jos_community_notifications`
ADD KEY `created` (`created`),
ADD KEY `status` (`status`),
ADD KEY `type` (`type`),
ADD KEY `target` (`target`),
ADD KEY `actor` (`actor`);


**********************************************
** SALES AND BILLING
** http://www.jomsocial.com/support/docs/108-sales-a-billing.html
**********************************************

Pre-Sales FAQ:
http://www.jomsocial.com/support/docs/item/709-pre-sales-faq.html

Licensing FAQ:
http://www.jomsocial.com/support/docs/item/852-licensing-faq.html

Downloading current and older version:
http://www.jomsocial.com/support/docs/item/710-downloading-jomsocial.html

Upgrading Subscription
http://www.jomsocial.com/support/docs/item/875-how-do-i-renew-upgrade-my-account.html

Refunding the subscription (awww):
http://www.jomsocial.com/support/docs/item/712-refund.html


**********************************************
** OFFICIAL SUPPORT POLICY
** http://www.jomsocial.com/support/overview.html
**********************************************

Official Answers Q&A:
http://www.jomsocial.com/support/answers.html

Feature Request Collective:
http://uservoice.jomsocial.com/forums/101561-feature-request


























