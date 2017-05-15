require 'capybara/poltergeist'

Capybara.run_server = false
Capybara.default_driver=:polergeist
Capybara.app_host="http://localhost:5984/"
