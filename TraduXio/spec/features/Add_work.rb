feature 'Add a work' do

  scenario 'With an original version' do
    data=random_work
    data[:no_original]=false
    create_work data
    expect(page).to have_content "#{data[:title]} – #{data[:author]}"
    expect(page).not_to have_content 'Trans.'
    expect(page).not_to have_selector 'a#editDoc'
    insert_work_text sample('the_lamp')
    expect(row(1)).to have_content 'THE LAMP'
    expect(row(2)).to have_content 'We found the lamp'
    expect(row(3)).to have_content 'No more was there'
  end

  scenario 'Without an original version (and a unknown author)' do
    data=random_work
    data[:no_original]=true
    data.delete(:author)
    create_work data
    expect(page).to have_content "#{data[:title]} – Anonymus"
    expect(page).to have_content 'Trans.'
    expect(page).to have_selector 'a#editDoc'
  end

  scenario 'Without an original version (and a known author)' do
    data=random_work
    data[:no_original]=true
    create_work data
    expect(page).to have_content "#{data[:title]} – #{data[:author]}"
    expect(page).to have_content 'Trans.'
    expect(page).to have_selector 'a#editDoc'
  end

  scenario 'Edit the unexisting original version' do
    data=random_work
    data[:no_original]=true
    create_work data
    expect(page).to have_content "#{data[:title]} – #{data[:author]}"
    expect(page).to have_content 'Trans.'
    expect(page).to have_selector 'a#editDoc'
    find('a#editDoc').trigger(:click)
    newdata=random_work
    newdata[:no_original]=true
    edit_work newdata
    expect(page).to have_content "#{newdata[:title]} – #{newdata[:author]}"
    expect(page).to have_content 'Trans.'
    expect(page).to have_selector 'a#editDoc'
  end

end
