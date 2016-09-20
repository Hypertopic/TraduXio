def create_translation(version)
  debug "click on add version button"
  page.find("a#addVersion").trigger(:click)
  debug "fill the creator #{version}"
  fill_in :'work-creator', :with => version
  begin
    debug "click on create button"
    #why do we need to click twice ????
    page.find('input[name=do-create]').click
    page.find('input[name=do-create]').click
    debug "wait #{version} to appear"
  end until has_translation?(version)
  debug "created #{version}"
end

def edit_translation_metadata(version,options)
  raise "Must pass a hash" if not options.is_a?(Hash)
  previously_in_edit_mode=edit_translation version
  edited=false
  within (selvers(version,"thead.header th.pleat.open")) do
    edited=fill_field(:date,options[:date]) if options.has_key?(:date)
    edited=fill_field(:title,options[:title]) if options.has_key?(:title)
    edited=fill_select(:language,options[:language]) if options.has_key?(:language)
    if version!=:original
      edited=fill_field(:'work-creator',options[:creator]) if options.has_key?(:creator)
      edited=fill_field(:creator,options[:author]) if options.has_key?(:author)
    else
      edited=fill_field(:'work-creator',options[:author]) if options.has_key?(:author)
    end
  end
  if edited then
    debug :blur
    edited.trigger(:blur)
  end
  if options.has_key?(:author)
    version=options[:author]
  end
  read_translation version if not previously_in_edit_mode
end

def random_translation_metadata
  { :author=>random_author,
    :title=>random_title,
    :date=>random_date,
    :language=>random_language,
    :creator=>random_author
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

def check_translation_metadata(metadata)
  translation=find_open_translation metadata[:author]
  expect(translation).to have_metadata(:date,metadata[:date]) if metadata.has_key?(:date)
  expect(translation).to have_metadata(:title,metadata[:title]) if metadata.has_key?(:title)
  expect(translation).to have_metadata(:creator,metadata[:author]) if metadata.has_key?(:author)
  expect(translation).to have_metadata(:'work-creator',metadata[:creator]) if metadata.has_key?(:creator)
  expect(translation).to have_metadata(:language,metadata[:language]) if metadata.has_key?(:language)
end

def fill_translation_text (author,number)
  edit_translation author

  text = random_text(number)

  text.each_with_index { |paragraph, index|
    fill_block(author,index,paragraph)
  }

  text
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

def check_text version,text
  text.each_with_index { |paragraph, index|
    debug "checking that block #{index} contains #{paragraph}"
    expect(block(version,index)).to have_content paragraph
  }
end

def block(version, row)
  table=page.find("table#hexapla")
  row=table.find("tr[data-line='#{row}']")
  row.find("td[data-version='#{version}']")
end

def getVersions
  page.evaluate_script("getVersions()")
end

def find_version(version,prefix="thead.header th.pleat",suffix="")
  if (suffix.length>0) then suffix=" #{suffix}" end
  find(selvers(version,prefix)+suffix)
end

def selvers(version,prefix="thead.header th.pleat")
  "#{prefix}[data-version='#{version}']"
end

def find_translation(version)
  find_version(version,"thead.header th.pleat")
end

def find_translation_footer(version)
  find_version(version,"thead.footer th.pleat")
end

def find_open_translation(version)
  find_version(version,"thead.header th.pleat.open")
end

def find_open_translation_footer(version)
  find_version(version,"thead.footer th.pleat.open")
end

def has_translation?(version)
  has_selector?(selvers(version,"th.pleat"))
end

def have_translation(version)
  have_selector(selvers(version,"th.pleat"))
end

def have_metadata(metadata,value)
  debug "check metata #{metadata}"
  if metadata != :language
    have_css("div.metadata.#{metadata}",:text=>value)
  else
    have_css("div.metadata.#{metadata}[title='#{value}']")
  end
end

def toggle_translation(version)
  debug "toggle translation #{version}"
  find_open_translation(version).find("input.edit").click
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
  debug "check translation #{version} open"
  find_translation(version)[:class].include?("open")
end

def is_edited?(version)
  debug "check translation #{version} edited"
  find_translation(version)[:class].include?("edit")
end

def delete_translation(version)
  open_translation version
  edit_translation version

  debug "delete #{version}"
  find_open_translation(version).find("span.delete").click
  debug "confirm deletion if #{version}"
  accept_alert
end
