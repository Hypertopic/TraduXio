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

# finds the row'th element having the .row class within
# the col'th element having the .col class
#
# <section id=translator>
#   <div class=col>
#     <p>not here !</p>
#   </div>
#   <div class=col>
#     <div class=row>
#       <p>not here either</p>
#     </div>
#     <div class=row>
#       <a href=/win>im here !</a>
#     </div>
#   </div>
# </section>
#
# => block(2, 2)
#
def block(col, row)
  within('#translator') do
    within(page.find(:xpath, ".//[@class='col']")[col]) do
      return page.find(:xpath, ".//[@class='row']")[row]
    end
  end
end
