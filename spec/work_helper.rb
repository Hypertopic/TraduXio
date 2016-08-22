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

def delete_full_work
  click_on "removeDoc"
  click_on "remove-confirm"
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
  click_on 'Create'
  wait_for_ajax
end

def edit_work_text (author,title,text)
  open_work author, title
  click_on 'Edit', :match => :first
  fill_in 'text', :with => text
  click_on 'Read', :match => :first
end
