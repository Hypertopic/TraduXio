require 'capybara/rspec'
require 'capybara/poltergeist'

Capybara.run_server = false
Capybara.default_driver = :poltergeist
Capybara.app_host = ENV['TEST_URL'] ? ENV['TEST_URL'] : 'http://127.0.0.1:5984/traduxio/_design/traduxio/_rewrite/'
puts "testing #{Capybara.app_host}"
Capybara.default_max_wait_time = 10

def do_debug?
  ENV['TEST_DEBUG'] ? true : false;
end

RSpec.configure do |config|
  config.before(:each) do
    prefer_language 'en'
  end
end

require 'work_helper'
require 'translation_helper'

def random_word(max_length)
  s = ('a'..'z').to_a.shuffle[0,1+rand*(max_length-1)].join
end

def random_author
  random_word(10)+" "+random_word(10)
end

def random_int(max)
  (rand*max).to_int
end

def random_boolean
  if random_int(2)>0 then
    true
  else
    false
  end
end

def random_text(nb_max)
  blocks=[]
  nb_paragraphs=nb_max
  n=0
  while n<nb_paragraphs
    blocks[n]=random_lines(8)
    n+=1
  end
  blocks
end

def random_lines(nb_max)
  lines=random_line
  nb_lines=random_int nb_max
  n=0
  while n<nb_lines
    lines+="\n"+random_line
    n+=1
  end
  lines
end

def random_line
  random_words 10,25
end

def random_title
  random_words 5,15
end

def random_words(nb_max,max_length)
  words=random_word(max_length)
  n=0
  nb_words=random_int nb_max
  while n<nb_words
    words+=" "+random_word(max_length)
    n+=1
  end
  words
end

def random_date
  1000+random_int(1000)
end

def random_language
  ['en','fr','he','el','pt','es','ar','ko'].shuffle[0,1].join
end

def sample(name)
  IO.read("spec/samples/#{name}.txt")
end

def row(row)
  find("#hexapla tr:nth-child(#{row}) .text")
end

def prefer_language(language)
  page.driver.add_headers("Accept-Language"=>language)
end

def have_in_bold(text)
  have_css('b', :text => text)
end

def wait_for_ajax
  Timeout.timeout(Capybara.default_max_wait_time) do
    loop until finished_all_ajax_requests?
  end
end

def finished_all_ajax_requests?
  page.evaluate_script('jQuery.active').zero?
end

def fill_input selector,value
  debug "fill field #{selector} with #{value}"
  input=find("input#{selector}")
  input.set(value)
  input
end

def fill_field(name,value)
  input=fill_input "[name=#{name}]",value
  wait_for_ajax
  input
end

def fill_select(name,option)
  input=find("select[name='#{name}']")
  option=input.find("option[value='#{option}']")
  select(option.text, :from => name)
  wait_for_ajax
  input
end

def fill_block(version,row,text)
  within block(version,row) do
    debug "filling block #{row} of #{version} with #{text}"
    ta=find('textarea')
    ta.set(text)
    ta.trigger(:blur)
    wait_for_ajax
  end
end

def block(version, row)
  table=page.find("table#hexapla")
  row=table.find("tr[data-line='#{row.to_s}']")
  row.find("td.edit[data-version='#{version}']")
end

def debug(step)
  print Time.new.strftime("%H:%M:%S")+" #{step}\n" if do_debug?
end
