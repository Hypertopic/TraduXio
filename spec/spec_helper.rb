require 'capybara/rspec'
require 'capybara/poltergeist'

Capybara.run_server = false
Capybara.default_driver = :poltergeist
Capybara.app_host = 'http://127.0.0.1:5984/traduxio/_design/traduxio/_rewrite/'

RSpec.configure do |config|
  config.before(:each) do
    prefer_language 'en'
  end
end

def sample(name)
  IO.read("spec/samples/#{name}.txt")
end

def row(row)
  find("#hexapla tr:nth-child(#{row}) .text")
end

def a_string()
	s = ('a'..'z').to_a.shuffle[0,8].join
end

def prefer_language(language)
	page.driver.add_headers("Accept-Language"=>language)
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
