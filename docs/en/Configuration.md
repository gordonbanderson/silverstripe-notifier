```yml
---
Name: mysitenotifications
Before: '#suilvennotifier'
---

---
Only:
  environment: 'live'
---
Suilven\Notifier\Notify:
  slack_webhooks:
    - name: default
      url: https://hooks.slack.com/services/WEBHOOK_HASH
---
Only:
  environment: 'dev'
---
Suilven\Notifier\Notify:
  channel_override: development

  slack_webhooks:
    #Channels are already preconfigured by webhook setup, but this can be overridden
    #A default channel must exist for Slack to work
    - name: default
      url: https://hooks.slack.com/services/WEBHOOK_HASH
```
