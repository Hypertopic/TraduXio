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

def find_open_translation(version)
  find("thead.header th.pleat.open[data-version='#{version}']")
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
  if is_edited?(version)
    toggle_translation version
  end
end

def edit_translation(version)
  if not is_edited?(version)
    toggle_translation version
  end
end

def is_open?(version)
  find_translation(version)[:class].include?("open")
end

def open_translation(version)
  if not is_open?(version)
    save_screenshot "closed.png"
    find_translation(version).find("span.button.show").click
  end
end

def close_translation(version)
  if is_open?(version)
    find_translation(version).find("span.button.hide").click
  end
end

def delete_translation(version)
  open_translation version
  edit_translation version

  debug "delete #{version}"
  find_open_translation(version).find("span.delete").click
  debug "confirm deletion if #{version}"
  accept_alert
end

def edit_translation_metadata(version,options)
  raise "Must pas a hash" if not options.is_a?(Hash)
  edit_translation version
  edited=false
  within ("thead.header th.pleat.open[data-version='#{version}']") do
    edited=fill_field('date',options.delete(:date)) if options.has_key?(:date)
    edited=fill_field('title',options.delete(:title)) if options.has_key?(:title)
    edited=fill_field('creator',options.delete(:creator)) if options.has_key?(:creator)
    edited=fill_select('language',options.delete(:language)) if options.has_key?(:language)
  end
  if edited then
    debug "blur"
    edited.trigger(:blur)
  end
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
