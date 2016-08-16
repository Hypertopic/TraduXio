require 'spec_helper'

feature 'Create a translation' do

  def check_translation_metadata(metadata)
    translation=find_open_translation metadata[:author]
    expect(translation).to have_metadata('date',metadata[:date]) if metadata.has_key?(:date)
    expect(translation).to have_metadata('title',metadata[:title]) if metadata.has_key?(:title)
    expect(translation).to have_metadata('language',metadata[:language]) if metadata.has_key?(:language)
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

    given!(:work_metadata) { create_random_work }

    scenario 'Create one translation' do
      metadata=create_random_translation
      debug metadata
      expect(page).to have_translation(metadata[:author])
      check_translation_metadata (metadata)
    end

    scenario 'Create two translations' do
      metadata1=create_random_translation
      expect(page).to have_translation(metadata1[:author])
      check_translation_metadata (metadata1)

      metadata2=create_random_translation
      expect(page).to have_translation(metadata2[:author])
      check_translation_metadata (metadata2)
    end

    scenario 'Edit translation text' do
      metadata=create_random_translation
      expect(page).to have_translation(metadata[:author])
      check_translation_metadata (metadata)

      open_translation metadata[:author]
      edit_translation metadata[:author]

      text = []

      4.times do |i|
        block=random_text(1)
        fill_block(metadata[:author],i,block)
        text[i]=block
      end
      read_translation(metadata[:author])
      debug "checking content"
      4.times do |i|
        expect(page).to have_content(text[i])
      end
      debug "checked content"

    end

    scenario 'Delete translation' do
      metadata1=create_random_translation
      metadata2=create_random_translation

      delete_translation metadata1[:author]
      expect(page).not_to have_translation metadata1[:author]
      delete_translation metadata2[:author]
      expect(page).not_to have_translation metadata2[:author]
    end

end
