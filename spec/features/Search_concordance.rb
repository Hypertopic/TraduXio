feature 'Search for a concordance' do

  def submitForm
    click_on "submit"
  end

  def select_and_search_concordance(version,block,s,e)
    debug "select in #{version} block #{block} between #{s} and #{e}"
    open_translation version
    debug current_path
    page.evaluate_script("findUnits('#{version}').filter(':eq(#{block})').selection('setPos',{start:#{s},end:#{e}})")
    expect(page).to have_css("div.context-menu div.item.concordance")
    find("div.context-menu div.item.concordance").trigger(:click)
  end

  scenario 'Search a valid sequence of words' do
    visit '/works/concordance'
    fill_input '#query', 'the ancient oil'
    fill_select 'language', 'en'
    submitForm()
    expect(page).to have_in_bold 'the ancient oil'
    expect(page).to have_content 'Trans. François Truchaud'
    expect(page).to have_content 'Trans. Aurélien Bénel'
  end

  scenario 'Search a sequence of words in the wrong order' do
    visit '/works/concordance'
    fill_input '#query', 'ancient the'
    fill_select 'language', 'en'
    submitForm()
    wait_for_ajax
    expect(page).not_to have_content 'Trans. Aurélien Bénel'
    expect(page).not_to have_content 'Trans. François Truchaud'
  end

  scenario 'Search the beginning of a word' do
    visit '/works/concordance'
    fill_input '#query', 'anc'
    fill_select 'language', 'en'
    submitForm()
    wait_for_ajax
    expect(page).to have_in_bold('anc')
    expect(page).to have_content 'Trans. François Truchaud'
    expect(page).to have_content 'Trans. Aurélien Bénel'
  end

  scenario 'Search in a text' do
    work_metadata=create_random_work
    open_work work_metadata[:author],work_metadata[:title]
    translation_metadata=create_random_translation
    translation_metadata[:text]=fill_translation_text(translation_metadata[:author],4)
    read_translation translation_metadata[:author]
    debug getVersions
    block=random_int(translation_metadata[:text].length)
    text=work_metadata[:text][block]
    word_n=random_int(16)
    word_nb=random_int(2)+1
    debug "select #{word_nb} words from #{word_n}th word"
    i=0
    startIndex=0
    debug "find space location in #{text}"
    while (i<word_n)
      r=text.index(" ",startIndex)
      if (r != nil) then startIndex=text.index(" ",startIndex)+1 end
      i+=1
    end
    debug "start index is #{startIndex}"
    endIndex=startIndex
    eol=text.index("\n",startIndex)
    if ! eol then eol=text.length end
    i=0
    while (i<word_nb && endIndex<=eol)
      r=text.index(" ",endIndex+1)
      if (r != nil) then endIndex=r else endIndex=text.length-1 end
      i+=1
    end
    if endIndex>eol then endIndex=eol end
    debug "end index is #{endIndex}"
    selected_text=text[startIndex..endIndex-1]
    debug "selected text is #{selected_text}"
    select_and_search_concordance getVersions[0],block,startIndex,endIndex
    expect(page).to have_content selected_text
    expect(page).to have_in_bold selected_text
  end

end
