# mailcow-prometheus-endpoint

This projet aims to provide a simple Prometheus endpoint for your mailcow.  

## Setup

### Script configuration
The configuration is done using the metrics.config.php.  
Inside the file you will need to define your own token `$token = '';` which is used to secure the metrics endpoint from unauthorized use.  
The mailcow hostname and the mailcow api key are set using url params by default using the url param `mailcow_api_key` and `mailcow_hostname`.  
Please note that you need to allow the IP of the webserver where the metrics.php is located inside of your mailcow API configuration otherwhise the metrics collection will not work.

### Example Prometheus config
```yml
  - job_name: 'mailcow'
    scrape_interval: 15s
    metrics_path: /metrics.php
    scheme: https
    params:
      token: ['<your token>']
      mailcow_api_key: ['<your mailcow api key>']
      mailcow_hostname: ['<your mailcow hostname>']
    honor_labels: True
    static_configs:
      - targets: ['<webserver where the metrics .php is located>']
```

## Dashboard

A example dashboard is included in this repo, inside of the assets/screenshots directory

![Dashboard](https://raw.githubusercontent.com/ntimo/mailcow-prometheus-endpoint/master/assets/screenshots/dashboard.png)
