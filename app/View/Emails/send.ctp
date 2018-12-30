<?php $this->Html->addCrumb('New Email', '#'); ?>

<div id="email_page" class="span12">
        <div class="row">
                <?php
                if (!empty($this->request->data)) {
                        $this->Html->link('Continue...', array("Controller" => 'e14', 'action' => 'index'));
                        echo $this->Form->create('Email', array('type' => 'post', 'url' => '/emails/send'));
                } else {
                        echo $this->Form->input('email', array('class' => 'email_form', 'label' => 'To: ', 'value' => $email));
                        echo $this->Form->input('subject', array('class' => 'email_form', 'label' => 'Subject: ', 'value' => $subject));
                        echo $this->Form->input('message', array('class' => 'email_form email_body', 'type' => 'textarea', 'label' => 'Message: ', 'value' => $message));
                        echo $this->Form->end('Send', array('class' => 'pull-right'));
                }
                ?>

        </div>
</div>
