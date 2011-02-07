{% include "ChatBundle:Track:base.twig.js" %}
{% include "ChatBundle:Track:update-timer.twig.js" %}

SGChatTrackerStatus = {{ online ? 'true' : 'false' }};
SGChatTracker.drawStatusLink();