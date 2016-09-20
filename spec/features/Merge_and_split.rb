feature 'Merge and split' do

  def merge_after(version,line)
    expect(work_line(line+1)).to have_selector(selvers(version,"td"))
    debug "merging #{version} after line #{line}"
    find_version(version, "tr[data-line='#{line+1}'] td").find("span.join").trigger(:click)
    wait_for_ajax
    expect(work_line(line+1)).not_to have_selector(selvers(version,"td"))
  end

  def split_on(version,line)
    expect(work_line(line)).not_to have_selector(selvers(version,"td"))
    debug "splitting #{version} on line #{line}"
    find_version(version, "#hexapla tr td","span.split[title='split line #{line}']").trigger(:click)
    wait_for_ajax
    expect(work_line(line+1)).to have_selector(selvers(version,"td"))
  end

  given!(:work_metadata) { create_random_work }
  given!(:translation_metadata) { create_random_translation }
  given!(:translation_text) { fill_translation_text(translation_metadata[:author],4) }

  scenario 'Merge' do
    read_translation translation_metadata[:author]
    merge_point=random_int(translation_text.length-1)
    debug "merge position is #{merge_point}"
    expect(block(translation_metadata[:author],merge_point)).to have_content(translation_text[merge_point])
    debug block(translation_metadata[:author],merge_point).text
    edit_translation translation_metadata[:author]
    debug "wait"
    merge_after translation_metadata[:author], merge_point
    read_translation translation_metadata[:author]
    expect(block(translation_metadata[:author],merge_point)).to have_content(translation_text[merge_point..merge_point+1].join("\n"))
    debug block(translation_metadata[:author],merge_point).text
    open_work work_metadata[:author], work_metadata[:title]
    open_translation translation_metadata[:author]
    expect(block(translation_metadata[:author],merge_point)).to have_content(translation_text[merge_point..merge_point+1].join("\n"))
  end

  scenario 'Merge then split' do
    read_translation translation_metadata[:author]
    merge_point=random_int(translation_text.length-1)
    debug "merge position is #{merge_point}"
    expect(block(translation_metadata[:author],merge_point)).to have_content(translation_text[merge_point])
    debug block(translation_metadata[:author],merge_point).text
    edit_translation translation_metadata[:author]
    debug "wait"
    merge_after translation_metadata[:author], merge_point
    read_translation translation_metadata[:author]
    expect(block(translation_metadata[:author],merge_point)).to have_content(translation_text[merge_point..merge_point+1].join("\n"))
    debug block(translation_metadata[:author],merge_point).text
    edit_translation translation_metadata[:author]
    split_on translation_metadata[:author], merge_point+1
    read_translation translation_metadata[:author]
    expect(block(translation_metadata[:author],merge_point)).to have_content(translation_text[merge_point..merge_point+1].join("\n"))
    expect(block(translation_metadata[:author],merge_point+1).text).to eq("")

    open_work work_metadata[:author], work_metadata[:title]
    open_translation translation_metadata[:author]
    expect(block(translation_metadata[:author],merge_point)).to have_content(translation_text[merge_point..merge_point+1].join("\n"))
    expect(block(translation_metadata[:author],merge_point+1).text).to eq("")
  end

end
