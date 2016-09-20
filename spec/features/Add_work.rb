feature 'Add a work' do

  scenario 'With an original version' do
    data=random_work
    data[:no_original]=false
    create_work data
    expect(page).to have_content "#{data[:title]} – #{data[:author]}"
    expect(page).not_to have_content 'Trans.'
    click_on 'Edit', :match => :first
    fill_in 'text', :with => sample('the_lamp')
    click_on 'Read', :match => :first
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
  end

  scenario 'Without an original version (and a known author)' do
    data=random_work
    data[:no_original]=true
    create_work data
    expect(page).to have_content "#{data[:title]} – #{data[:author]}"
    expect(page).to have_content 'Trans.'
  end

end
