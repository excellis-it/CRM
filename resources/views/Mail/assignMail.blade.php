@component('mail::message')
# Introduction

Hi {{ $maildata['user']['name'] }},

I’m sending this email to assign you in {{ $maildata['project']['title'] }} project. This is a very important project , so I’d like you to make this task a top priority.

Thanks,<br>
ExcellisIt
@endcomponent

