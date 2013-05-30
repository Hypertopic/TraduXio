require 'capybara/rspec'
require 'capybara/webkit'

Capybara.run_server = false
Capybara.default_driver = :webkit
Capybara.javascript_driver = :webkit
Capybara.app_host = 'http://traduxio.test.hypertopic.org/'

def a_string()
	s = ('a'..'z').to_a.shuffle[0,8].join
end

def prefer_language(language)
	page.driver.header 'Accept-Language', language
end

def in_bold()
	page.all(:css, "b")
end