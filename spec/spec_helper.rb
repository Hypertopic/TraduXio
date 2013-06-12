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

def should_have_in_bold(text)
	page.should have_css('b', :text => text)
end

# find a element in the page having an ID of #blockX (X being a integer)
# can be useful to find a block of text to translate or to get content from
def block(number)
	myId = "#block" + number.to_s
	page.find(:css, myId)
end
