require 'capybara/rspec'
require 'capybara/poltergeist'

Capybara.run_server = false
Capybara.default_driver = :poltergeist
Capybara.app_host = 'http://127.0.0.1:5984/traduxio/_design/traduxio/_rewrite/'
Capybara.default_max_wait_time = 5

def do_debug?
  false
end

RSpec.configure do |config|
  config.before(:each) do
    prefer_language 'en'
  end
end

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
  if random_int(2) then
    true
  else
    false
  end
end

def random_text(nb_max)
  text=random_lines 8
  nb_paragraphs=nb_max
  n=0
  while n<nb_paragraphs
    text+="\n\n"+random_lines(8)
    n+=1
  end
  text
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

def open_work(author,title)
  debug "visit"
  visit '/works/'
  debug "check author #{author}"
  expect(page).to have_css('li.author.closed',:text=>author)
  debug "open author #{author}"
  page.find('li.author.closed',:text=>author).trigger(:click)
  debug "check work #{title}"
  expect(page).to have_css('a',:text=>title)
  debug "click work #{title}"
  click_on title
  debug "opened"
end

def find_translation(version)
  find("thead.header th.pleat[data-version='#{version}']")
end

def find_translation_footer(version)
  find("thead.footer th.pleat[data-version='#{version}']")
end

def find_open_translation(version)
  find("thead.header th.pleat.open[data-version='#{version}']")
end

def find_open_translation_footer(version)
  find("thead.footer th.pleat.open[data-version='#{version}']")
end

def has_translation?(version)
  has_selector?("th.pleat[data-version='#{version}']")
end

def have_translation(version)
  have_selector("th.pleat[data-version='#{version}']")
end

def have_metadata(metadata,value)
  if metadata != "language"
    have_css("div.metadata.#{metadata}",:text=>value)
  else
    have_css("div.metadata.#{metadata}[title='#{value}']")
  end
end

def is_edited?(version)
  expect(page).to have_translation(version)
  debug "check translation #{version} edited"
  find_translation(version)[:class].include?("edit")
end

def toggle_translation(version)
  debug "toggle translation #{version}"
  find_open_translation(version).find("input.edit").click
end

def read_translation(version)
  read=!is_edited?(version)
  if not read
    toggle_translation version
  end
  read
end

def edit_translation(version)
  edited=is_edited?(version)
  if not edited
    toggle_translation version
  end
  edited
end

def is_open?(version)
  find_translation(version)[:class].include?("open")
end

def open_translation(version)
  if not is_open?(version)
    debug "open translation #{version}"
    find_translation(version).find("span.button.show").click
  end
end

def close_translation(version)
  if is_open?(version)
    find_translation(version).find("span.button.hide").click
  end
end

def delete_full_work
  click_on "removeDoc"
  click_on "remove-confirm"
  accept_alert
end

def delete_translation(version)
  open_translation version
  edit_translation version

  debug "delete #{version}"
  find_open_translation(version).find("span.delete").click
  debug "confirm deletion if #{version}"
  accept_alert
end

def change_license(version)
  find_open_translation_footer(version).find("div.button.edit-license").click
end

def random_work
  {:title=>random_title,:author=>random_author,:date=>random_date,:language=>random_language}
end

def create_random_work
  metadata=random_work
  debug metadata
  if random_boolean then
    metadata[:no_original]=true
  end
  debug metadata
  create_work metadata
  metadata[:text]=random_text(5)
  edit_work_text(metadata[:author],metadata[:title],metadata[:text])
  metadata
end

def create_work(options)
  visit '/'
  click_on 'Start'
  click_on 'Add a work'
  fill_in 'Title', :with => options[:title] if options.has_key?(:title)
  fill_in 'Author', :with => options[:author] if options.has_key?(:author)
  fill_select 'language',options[:language] if options.has_key?(:language)
  fill_in 'Date, year, or text century', :with=>options[:date] if options.has_key?(:date)
  if options.has_key?(:no_original) && options[:no_original] then
    debug "no original"
    uncheck 'Original work'
  else
    debug "original"
    check 'Original work'
  end
  save_screenshot "work_create.png"
  click_on 'Create'
  wait_for_ajax
end

def edit_work_text (author,title,text)
  open_work author, title
  click_on 'Edit', :match => :first
  fill_in 'text', :with => text
  click_on 'Read', :match => :first
end

def edit_translation_metadata(version,options)
  raise "Must pass a hash" if not options.is_a?(Hash)
  previously_in_edit_mode=edit_translation version
  edited=false
  within ("thead.header th.pleat.open[data-version='#{version}']") do
    edited=fill_field('date',options[:date]) if options.has_key?(:date)
    edited=fill_field('title',options[:title]) if options.has_key?(:title)
    edited=fill_field('creator',options[:creator]) if options.has_key?(:creator)
    edited=fill_select('language',options[:language]) if options.has_key?(:language)
  end
  if edited then
    debug "blur"
    edited.trigger(:blur)
  end
  read_translation version if not previously_in_edit_mode
end

def create_translation(version)
  debug "click on add version button"
  page.find("a#addVersion").trigger(:click)
  debug "fill the creator #{version}"
  fill_in 'work-creator', :with => version
  begin
    debug "click on create button"
    #why do we need to click twice ????
    page.find('input[name=do-create]').click
    page.find('input[name=do-create]').click
    debug "wait #{version} to appear"
  end until has_translation?(version)
  debug "created #{version}"
end

def debug(step)
  print Time.new.strftime("%H:%M:%S")+" #{step}\n" if do_debug?
end
