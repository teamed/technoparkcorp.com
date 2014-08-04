#!/usr/bin/ruby

require 'fileutils'
require 'nokogiri'

Dir.glob('src/content/**/*.xml') do |file|
  puts file
  xml = Nokogiri::XML(File.read(file))
  md = "---\n"
  if xml.xpath('/article/label/text()').length > 0
    md += "label: " + xml.xpath('/article/label/text()')[0] + "\n"
  end
  md += "title: \"" + xml.xpath('/article/title/text()')[0] \
    .to_s.split.map(&:capitalize)*' ' + "\"\n"
  if xml.xpath('/article/intro/text()').length > 0
    md += "intro: \"" + xml.xpath('/article/intro/text()')[0] \
      .to_s.strip.gsub(/\s+/, ' ') + "\"\n"
  end
  if xml.xpath('/article/description/text()').length > 0
    md += "description: |" \
      + xml.xpath('/article/description/text()')[0] \
        .to_s.strip.gsub(/\s+/, ' ') \
        .gsub(/(.{1,60})(\s+|\Z)/, "\n  \\1") + "\n"
  end
  if xml.xpath('/article/keywords/text()').length > 0
    md += "keywords:\n  - " \
      + xml.xpath('/article/keywords/text()')[0] \
        .to_s.strip.split(",").map(&:strip).join("\n  - ") + "\n"
  end
  if xml.xpath('/article/next/text()').length > 0
    md += "next: " + xml.xpath('/article/next/text()')[0].to_s.strip + "\n"
  end
  md += "---\n\n"
  output = "output/#{File.dirname(file)}/#{File.basename(file,'.xml')}.md"
  FileUtils.mkdir_p(File.dirname(output))
  File.write(output, md)
  puts output
end
