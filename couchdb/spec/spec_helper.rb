﻿require 'capybara/rspec'
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

# finds the row'th element having the .row class within
# the col'th element having the .col class
#
# <div id=translator>
#   <div class=col>
#     <p>not here !</p>
#   </div>
#   <div class=col>
#     <div class=row>
#       <p>not here either</p>
#     </div>
#     <div class=row>
#       <input name=foo />
#     </div>
#   </div>
# </div>
#
# => block(2, 2)
#
def block(col, row)
  page.find(:xpath, "//*[@id='translator']/div[@class='col']["+col.to_s+"]/div[@class='row']["+row.to_s+"]/*")['name']
end
