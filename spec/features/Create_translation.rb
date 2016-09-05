require 'spec_helper'

feature 'Create a translation' do

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

      text = fill_translation_text(metadata[:author],4)

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
