# mailchimpAPI-move-copy
##Move or Copy Subscribers from one Mailchimp list to another.
This class uses Mailchimp API wrappers provided by Drew McLellan

Download or clone the repository
Instantiate the class
  $Manager = new MailChimpManager($apiKey);>

To move/copy subscribers, call the following functions with the List ID
  $batch_id = $Manager->moveSubscribers('fromListId', 'toListId');
  $batch_id = $up->copySubscribers('fromListId', 'toListId');


###Improvements are very welcome, enjoy!
