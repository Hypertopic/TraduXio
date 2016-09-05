def check_translation_metadata(metadata)
  translation=find_open_translation metadata[:author]
  expect(translation).to have_metadata('date',metadata[:date]) if metadata.has_key?(:date)
  expect(translation).to have_metadata('title',metadata[:title]) if metadata.has_key?(:title)
  expect(translation).to have_metadata('language',metadata[:language]) if metadata.has_key?(:language)
end

def fill_translation_text (author,number)
  edit_translation author

  text = []

  number.times do |i|
    block=random_text(1)
    fill_block(author,i,block)
    text[i]=block
  end

  text
end

def random_translation_metadata
  { :author=>random_author,
    :title=>random_title,
    :date=>random_date,
    :language=>random_language
  }
end

def create_random_translation
  data=random_translation_metadata
  create_translation data[:author]
  edit_translation_metadata(data[:author],data)
  read_translation data[:author]
  debug data
  data
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

def delete_translation(version)
  open_translation version
  edit_translation version

  debug "delete #{version}"
  find_open_translation(version).find("span.delete").click
  debug "confirm deletion if #{version}"
  accept_alert
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
