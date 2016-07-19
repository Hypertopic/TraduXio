require 'spec_helper'

feature 'Search for a concordance' do

  def submitForm
    click_on "Search"
  end

  scenario 'Search a valid sequence of words' do
    visit '/works/concordance'
    save_screenshot "concordance1.png"
    fill_input '#query', 'the ancient oil'
    fill_select 'language', 'en'
    submitForm()
    save_screenshot "concordance1.png"
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
    save_screenshot "concordance2.png"
    expect(page).not_to have_content 'Trans. Aurélien Bénel'
    expect(page).not_to have_content 'Trans. François Truchaud'
  end

  scenario 'Search the beginning of a word' do
    visit '/works/concordance'
    save_screenshot "concordance.png"
    fill_input '#query', 'anc'
    fill_select 'language', 'en'
    submitForm()
    wait_for_ajax
    save_screenshot "concordance3.png"
    expect(page).to have_in_bold('anc')
    expect(page).to have_content 'Trans. François Truchaud'
    expect(page).to have_content 'Trans. Aurélien Bénel'
  end

end
