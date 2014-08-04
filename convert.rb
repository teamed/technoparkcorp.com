#!/usr/bin/ruby

require 'nokogiri'

Dir.glob('src/content/**/*.xml') do |file|
  xml = Nokogiri::XML(File.read(file))
  md = "---\n"
  md += "title: " + xml.xpath('/article/title/text()')[0] + "\n"
  puts file
end
